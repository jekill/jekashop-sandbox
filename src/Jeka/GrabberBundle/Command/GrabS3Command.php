<?php

namespace Jeka\GrabberBundle\Command;

use \Jeka\ToolsBundle\Text\TextTools;
use \Application\Vespolina\ProductBundle\Document\Product;
use \Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GrabS3Command extends ContainerAwareCommand
{

    //private $startUrl = 'http://www.s3.ru/ru/catalogue/32111/index.php?set_filter=Y&arrFilter_pf[brend]=IMAGE+ART&SHOWALL_1=1';
    //private $startUrl = 'http://www.s3.ru/ru/catalogue/13905/?SHOWALL_1=1';
    //private $startUrl = 'http://www.s3.ru/ru/catalogue/35396/?SHOWALL_1=1';

    //private $startUrl = 'http://www.s3.ru/ru/catalogue/32108/index.php?set_filter=Y&arrFilter_pf[brend]=IMAGE+ART';
    //private $startUrl = 'http://www.s3.ru/ru/catalogue/32109/index.php?set_filter=Y&arrFilter_pf[brend]=PATA';
    //private $startUrl = 'http://www.s3.ru/ru/catalogue/32108/index.php?set_filter=Y&arrFilter_pf[brend]=PATA&SHOWALL_1=1';
    private $startUrl = 'http://www.s3.ru/ru/catalogue/32104/';


    private $textTools;

    /**
     * @var OutputInterface
     */
    private $output;


    public function configure()
    {
        $this->setName("shop:grabber:s3");
        $this->textTools = new TextTools();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->output = $output;

        $product_links = array();
        $urls = array($this->startUrl);

        foreach ($urls as $url)
        {
            $output->writeln("<info>Products list page '{$url}'</info>");
            $product_links = array_merge($product_links, $this->fetchLinks($url));
        }

        foreach ($product_links as $prod_link) {
            $this->processProdPage($prod_link);
            $sleep = mt_rand(0,2000);
            $this->output->writeln("<warn>Sleep $sleep ms</warn>");
            usleep($sleep);
        }


    }

    private function fetchLinks($parse_page_url)
    {
        //print iconv('cp1251','utf-8',file_get_contents($parse_page_url));exit;
        $crawl = new Crawler(file_get_contents($parse_page_url), $parse_page_url);

        $links_to_prod = array();
        $links = $crawl->filter('a.list_header')->links();
        foreach ($links as $l)
        {
            $links_to_prod[] = $l->getUri();
        }

        return $links_to_prod;

    }

    private function processProdPage($prod_link, $categories = null)
    {
        $this->output->writeln("<info>Process product page '$prod_link'</info>");

        $html = file_get_contents($prod_link);
        //$html = iconv('cp1251','utf-8',file_get_contents($prod_link));
        //$html = str_replace('<head>','<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />',$html);
        //print $html;exit;
        $crawl = new Crawler($html);
        $title = $crawl->filter('h1.header')->text();
        $category_title = $crawl->filter('h3.prodname')->text();

        /** @var $cm \Jeka\CategoryBundle\Document\CategoryManager */
        $cm = $this->getContainer()->get('jeka.category_manager');
        $category = $cm->getRepository()->findOneBy(array('name' => $category_title));
        if (!$category) {
            $root_category = $cm->findBySlug('photoalbums');
            $category = $cm->createCategory();
            $category->setName($category_title);
            $category->setSlug($this->textTools->slugify($category_title));
            $category->setParent($root_category);
            $this->output->writeln("\tCreating category $category_title");
            $cm->updateCategory($category);
        }

        $this->output->writeln("\tProduct $title");

        $prod_price = $crawl->filter('.catalog-price')->text();
        $prod_price = (float)preg_replace('/[^\d\.]/','',$prod_price);


        /** @var $pm \Jeka\ShopBundle\Document\ProductManager */
        $pm = $this->getContainer()->get('vespolina.product_manager');
        /** @var $product Product */
        $product = $pm->createProduct();
        $product->setName($title);
        $product->setSlug($this->textTools->slugify($title));
        $product->setPrice($prod_price);
        $product->addCategories($category);
        $product->setType(Product::PHYSICAL);


        // features

        $features_table = $crawl->filter('.properties');

        $artikul ='';
        foreach($features_table->filter('tr') as $index =>$tr)
        {
            if ($index==0) continue;
            /** @var $tds DOMNodeList */
            $tds = $tr->getElementsByTagName('td');
            $f = new \Application\Vespolina\ProductBundle\Document\Feature();
            $f->setType($tds->item(0)->nodeValue);
            $f->setName($tds->item(2)->nodeValue);
            $f->setSearchTerm($tds->item(0)->nodeValue);
            $f->setPos($index);
            //var_dump($f);exit;
            $product->addFeature($f);
            if ($f->getType()=='Артикул')
            {
                $artikul=$f->getName();
            }
        }


        $images = $crawl->filter(".catalog-element img[src^='/upload']");


        $pos = count($images);
        /** @var $im \Jeka\ImageBundle\Document\ImageManager */
        $im = $this->getContainer()->get('jeka.image_manager');
        foreach ($images as $i)
        {
            $src  = $i->getAttribute('src');
            if (!$src) continue;
            $image_url = 'http://www.s3.ru' . $src;
            $tmpfile = tempnam(sys_get_temp_dir(), 'image');
            $this->output->writeln("<info>\t\t- Download image '$image_url'</info>");
            $imagedata = @file_get_contents($image_url);
            if (!$imagedata) continue;
            file_put_contents($tmpfile, $imagedata);
            $image = $im->createImageFromFile($tmpfile);
            unlink($tmpfile);
            $image->setPos($pos--);
            $im->persist($image);
            $product->addImages($image);
        }

        $s3id = new \Application\Vespolina\ProductBundle\Document\S3Identifier();
        $artikul_id  = new \Vespolina\ProductBundle\Document\SKUIdentifier();
        //$idkey = array('second'=>'second');
        if ($artikul)
        {
            $artikul_id->setCode($artikul);
            $product->addIdentifier($artikul_id);
        }

        $s3art = $crawl->filter('.gray')->text();
        if ($s3art)
        {
            $s3id->setCode($s3art);
            $product->addIdentifier($s3id);
        }

        $pm->updateProduct($product);
    }


}
