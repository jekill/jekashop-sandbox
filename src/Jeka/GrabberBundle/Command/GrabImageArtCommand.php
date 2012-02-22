<?php

namespace Jeka\GrabberBundle\Command;

use \Jeka\ToolsBundle\Text\TextTools;
use \Application\Vespolina\ProductBundle\Document\Product;
use \Symfony\Component\DomCrawler\Crawler;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GrabImageArtCommand extends ContainerAwareCommand
{

    private $startUrl = 'http://www.imageartcorp.ru/ru/catalog/photoalbums/deluxe/';
    private $textTools;

    /**
     * @var OutputInterface
     */
    private $output;

    public function configure()
    {
        $this->setName("shop:grabber:imageart");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        //print preg_match("~(/common/img/changed2/.+?')~", "javascript: showGallery('/common/img/changed2/29059d1.jpg');return false;");exit;

//        $m = array();
//        $image_prefix = '/common/img/changed2/';
//        print preg_match("~($image_prefix.+?')~","javascript: showGallery('/common/img/changed2/47185d2.jpg')",$m);
//        print_r($m);exit;
//        $html = <<<END
//        <img width="72" height="72" border="3" alt="Фотоальбом Image Art 200 10x15 (BBM46200/ 2) (12)  Imperial" class="border3" src="/common/img/changed2/52666d1s.jpg"/></a>
//        <a onclick="javascript: showGallery('/common/img/changed2/52666d2.jpg');return false;" href="#"><img width="72" height="72" border="3" alt="Фотоальбом Image Art 200 10x15 (BBM46200/ 2) (12)  Imperial" class="border3" src="/common/img/changed2/52666d2s.jpg"/>
//END;
//        $crawl= new Crawler($html);
//        $r = $crawl->filter("a[onclick!='showGallery']");
//        var_dump($r);exit;
//
        $this->textTools = new TextTools();

        $this->output = $output;

        $product_links = array();
        $urls = array($this->startUrl);
        $urls = array_merge($urls, $this->getPagesUrls($this->startUrl));

        foreach ($urls as $url)
        {
            $output->writeln("<info>Products list page '{$url}'</info>");
            $product_links = array_merge($product_links, $this->fetchLinks($url));
        }

        foreach ($product_links as $prod_link) {
            $this->processProdPage($prod_link);
        }


    }

    private function fetchLinks($parse_page_url)
    {
        //print iconv('cp1251','utf-8',file_get_contents($parse_page_url));exit;
        $crawl = new Crawler(file_get_contents($parse_page_url), $parse_page_url);

        $links_to_prod = array();
        $links = $crawl->filter('a[href^="/ru/catalog/photoalbums/deluxe/detail.php?"]')->links();
        foreach ($links as $l)
        {
            $links_to_prod[] = $l->getUri();
        }

        return $links_to_prod;

    }

    private function getPagesUrls($url)
    {
        $crawl = new Crawler(file_get_contents($url), $url);
        $res = $crawl->filter("a.grey");
        $links = array();
        foreach ($res->links() as $l)
        {
            $links[] = $l->getUri();
        }

        return $links;
    }

    private function processProdPage($prod_link, $categories = null)
    {
        $this->output->writeln("<info>Process product page '$prod_link'</info>");

        $html = iconv('cp1251','utf-8',file_get_contents($prod_link));
        $html = str_replace('<head>','<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />',$html);
        //print $html;exit;
        $crawl = new Crawler($html);
        $title = $crawl->filter('h3')->text();
        $category_title = $crawl->filter('h2')->text();


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

        /** @var $pm \Jeka\ShopBundle\Document\ProductManager */
        $pm = $this->getContainer()->get('vespolina.product_manager');
        /** @var $product Product */
        $product = $pm->createProduct();
        $product->setName($title);
        $product->setSlug($this->textTools->slugify($title));
        $product->setPrice(rand(500,1550));
        $product->addCategories($category);
        $product->setType(Product::PHYSICAL);

        $image_prefix = '/common/img/changed2/';

        $images = array();
        try{
            $images[] = $crawl->filter('img[src^="' . $image_prefix . '"]')->first()->attr('src');
        }catch(\Exception $e){
            return;
        }

        $crawl->filter('a[onclick*="' . $image_prefix . '"]')->each(
            function($node, $i) use($image_prefix, &$images)
            {
                $onclick = $node->getAttribute('onclick');
                $m = array();
                if (preg_match("~($image_prefix.+?)'~", $onclick, $m)) {
                    $images[] = $m[1];
                }
            });


        $pos = count($images);
        /** @var $im \Jeka\ImageBundle\Document\ImageManager */
        $im = $this->getContainer()->get('jeka.image_manager');
        foreach ($images as $src)
        {
            if (!$src) continue;
            $image_url = 'http://www.imageartcorp.ru' . $src;
            $tmpfile = tempnam(sys_get_temp_dir(), 'image');
            $this->output->writeln("<info>\t\t- Download image '$image_url'</info>");
            file_put_contents($tmpfile, file_get_contents($image_url));
            $image = $im->createImageFromFile($tmpfile);
            unlink($tmpfile);
            $image->setPos($pos--);
            $im->persist($image);
            $product->addImages($image);

        }

        $pm->updateProduct($product);
    }


}
