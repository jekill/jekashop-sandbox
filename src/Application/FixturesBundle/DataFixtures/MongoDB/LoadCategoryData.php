<?php
namespace Application\FixturesBundle\DataFixtures\ORM;

use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Jeka\CategoryBundle\Document\Category;
use \Vespolina\ProductBundle\Document\Feature;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Application\Vespolina\ProductBundle\Document\Product;


class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\ODM\MongoDB\DocumentManager $manager
     */
    function load($manager)
    {
        $cat1 = new Category();
        $cat1->setName('Все товары');
        $cat1->setSlug('root');
        $cat1->setType('shop');

        $manager->persist($cat1);

        $cat2=new Category();
        $cat2->setName('Фотоальбомы');
        $cat2->setSlug('photoalbums');
        $cat2->setParent($cat1);
        $cat2->addAncestors($cat1);

        $manager->persist($cat2);

        $cat3 = new Category();
        $cat3->setName('Свадебные фотоальбомы');
        $cat3->setSlug('wedding-albums');
        $cat3->setParent($cat2);
        $cat3->addAncestors($cat1);
        $cat3->addAncestors($cat2);

        $manager->persist($cat3);

        $cat4 = new Category();
        $cat4->setName('Детские фотоальбомы');
        $cat4->setSlug('kids-albums');
        $cat4->setParent($cat2);
        $cat4->addAncestors($cat1);
        $cat4->addAncestors($cat2);

        $manager->persist($cat4);

        $cat_uslugi = new Category();
        $cat_uslugi->setName('Услуги');
        $cat_uslugi->setSlug('services');
        $cat_uslugi->setParent($cat1);
        $cat_uslugi->setIsHidden(true);
        $cat_uslugi->addAncestors($cat1);

        $manager->persist($cat_uslugi);


        $cat_frames = new Category();
        $cat_frames->setName('Фоторамки');
        $cat_frames->setSlug('phtoframes');
        $cat_frames->setParent($cat1);
        $cat_frames->addAncestors($cat1);
        $manager->persist($cat_frames);


        $manager->flush();

        $this->addReference('cat_1', $cat1);
        $this->addReference('cat_1_1', $cat2);
        $this->addReference('cat_1_1_1', $cat3);
        $this->addReference('cat_1_1_2', $cat4);
        $this->addReference('cat_1_2',$cat_uslugi);
        $this->addReference('cat_1_3',$cat_frames);
    }


    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 10;
    }
}
