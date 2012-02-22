<?php
namespace Application\FixturesBundle\DataFixtures\ORM;

use \Jeka\ImageBundle\Tools\ImageModifier;
use \Jeka\ImageBundle\Document\Image;
use \Doctrine\Common\DataFixtures\AbstractFixture;
use \Vespolina\ProductBundle\Document\Feature;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;



class LoadImageData extends AbstractFixture implements OrderedFixtureInterface, \Symfony\Component\DependencyInjection\ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private  $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\ODM\MongoDB\DocumentManager $manager
     */
    function load($manager)
    {
        $im = new ImageModifier($this->container);
        for($i=1;$i<=7;$i++)
        {
            $image = $im->createImageFromFile(__DIR__.'/../files/image'.$i.'.jpg');

            $manager->persist($image);
            $this->addReference('image_'.$i,$image);
        }

        $manager->flush();


    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 15;
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
