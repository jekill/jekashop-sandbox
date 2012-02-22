<?php
namespace Application\Vespolina\CartBundle\Document;

use Vespolina\CartBundle\Document\CartManager as BaseManager;

class CartManager extends BaseManager
{

    public function saveAsLocked(Cart $cart)
    {
        $counter = $this->dm->createQueryBuilder('Jeka\ShopBundle\Document\Counter')
            ->findAndUpdate()
            ->returnNew()
            ->field('id')->equals('cart')
            ->field('next')->inc(1)
            ->getQuery()
            ->execute();
        if (!$counter){
            $counter = new \Jeka\ShopBundle\Document\Counter();
            $counter->setId('cart');
            $counter->setNext(1);
            $this->dm->persist($counter);
            $this->dm->flush();
        }

        $cart->setState(Cart::STATE_LOCKED);
        $cart->setNumber($counter->getNext());
        $this->updateCart($cart);

        return true;
    }
}