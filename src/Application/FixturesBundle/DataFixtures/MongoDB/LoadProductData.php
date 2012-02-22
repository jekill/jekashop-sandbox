<?php

namespace Application\FixturesBundle\DataFixtures\ORM;

use \Application\Vespolina\ProductBundle\Document\OptionGroup;
use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Vespolina\ProductBundle\Document\OptionSet;
use \Vespolina\ProductBundle\Document\Option;
use \Vespolina\ProductBundle\Document\Feature;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Application\Vespolina\ProductBundle\Document\Product;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadProductData extends AbstractFixture implements OrderedFixtureInterface, \Symfony\Component\DependencyInjection\ContainerAwareInterface
{

    private  $container;
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\ODM\MongoDB\DocumentManager $manager
     */
    function load($manager)
    {
        $feature1 = new Feature();
        $feature1->setType('size');
        $feature1->setName('20x20');
        $feature1->setSearchTerm('size');

        $feature2 = new Feature();
        $feature2->setType('weight');
        $feature2->setName('31');
        $feature2->setSearchTerm('weight');

        $feature3 = new Feature();
        $feature3->setType('pages');
        $feature3->setName('230');
        $feature3->setSearchTerm('pages');

        $o_gr = new OptionGroup();
        $o_gr->setName("color");
        $o_gr->setRequired(1);
        $o_gr->setDisplay('my');


        $opt = new Option();
        $opt->setType('color');
        $opt->setValue('white');

        $opt2 = new Option();
        $opt2->setType('color');
        $opt2->setValue('green');


        $o_gr->addOption($opt);
        $o_gr->addOption($opt2);

        /** @var $pm \Jeka\ShopBundle\Document\ProductManager */
        $pm = $this->container->get('vespolina.product_manager');

        $prod = $pm->createProduct();
        $prod->setPrice(110.2);
        $prod->setName('Test product');
        $prod->setDescription('Test product\'s description');
        $prod->addCategories($this->getReference('cat_1_1_1'));
        $prod->addImages($this->getReference('image_1'));
        $prod->addImages($this->getReference('image_2'));
        $prod->addImages($this->getReference('image_3'));
        $prod->setSlug('test-product');
        $prod->setType(Product::PHYSICAL);

        $prod->addOptionGroup($o_gr);

        $prod->addFeature($feature1);
        $prod->addFeature($feature2);
        $prod->addFeature($feature3);

        $manager->persist($prod);

        $feature1->setName('23x10');
        $feature2->setName('9');
        $feature3->setName('114');

        $prod1 = $pm->createProduct();
        $prod1->setPrice(55.99);
        $prod1->setName('Бутылка пива');
        $prod1->setSlug('butilka-piva');

        $prod1->setDescription('Описание товара');
        $prod1->addCategories($this->getReference('cat_1_1_1'));
        $prod1->addImages($this->getReference('image_2'));
        $prod1->addImages($this->getReference('image_3'));
        $prod1->addImages($this->getReference('image_4'));
        $prod1->setType(Product::PHYSICAL);
//        $prod1->addFeature($feature1);
//        $prod1->addFeature($feature2);
//        $prod1->addFeature($feature3);

        $manager->persist($prod1);


        $feature1->setName('30x50');
        $feature2->setName('19');
        $feature3->setName('184');

        $prod2 = $pm->createProduct();
        $prod2->setPrice(1250);
        $prod2->setName('Фотоальбом');
        $prod2->setSlug('fotoalbom');
        $prod2->setDescription('Описание товара2');
        $prod2->addCategories($this->getReference('cat_1_1_1'));
        $prod2->addCategories($this->getReference('cat_1_1_2'));
        $prod2->addImages($this->getReference('image_3'));
        $prod2->addImages($this->getReference('image_4'));
        $prod2->addImages($this->getReference('image_5'));
        $prod2->setType(Product::PHYSICAL);

//        $prod2->addFeature($feature1);
//        $prod2->addFeature($feature2);
//        $prod2->addFeature($feature3);

        $manager->persist($prod2);


        $prod3 = $pm->createProduct();
        $prod3->setName('Доставка курьером');
        $prod3->setSlug('courier-delivery');
        $prod3->setType(Product::SERVICE|Product::UNIQUE);
        $prod3->setPrice('200');
        $prod3->addCategories($this->getReference('cat_1_2'));
//        $prod->addImages($this->getReference('image_5'));
//        $prod->addImages($this->getReference('image_6'));
//        $prod->addImages($this->getReference('image_7'));
        $manager->persist($prod3);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 20;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}