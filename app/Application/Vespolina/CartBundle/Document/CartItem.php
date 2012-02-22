<?php

namespace Application\Vespolina\CartBundle\Document;



/**
 * Application\Vespolina\CartBundle\Document\CartItem
 */
class CartItem extends \Vespolina\CartBundle\Document\BaseCartItem
{
    

    public function __construct($cartableItem = null)
    {
        parent::__construct($cartableItem);
    }



    /**
     * @var Application\Vespolina\ProductBundle\Document\Product
     */
    protected $cartableItem;


    /**
     * Set cartableItem
     *
     * @param Application\Vespolina\ProductBundle\Document\Product $cartableItem
     */
    public function setCartableItem(\Application\Vespolina\ProductBundle\Document\Product $cartableItem)
    {
        $this->cartableItem = $cartableItem;
    }

    /**
     * Get cartableItem
     *
     * @return Application\Vespolina\ProductBundle\Document\Product $cartableItem
     */
    public function getCartableItem()
    {
        return $this->cartableItem;
    }

}
