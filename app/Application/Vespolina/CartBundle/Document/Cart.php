<?php
namespace Application\Vespolina\CartBundle\Document;

use Vespolina\CartBundle\Document\BaseCart;
use Vespolina\CartBundle\Model\CartItemInterface;

class Cart extends BaseCart
{

    protected $totalQuantity = 0;

    /**
     * @var MongoId $id
     */
    protected $id;

    /**
     * @var object
     */
    protected $items = array();

    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param $index int
     * @return \Vespolina\CartBundle\Model\CartableItemInterface
     */
    public function getItem($index)
    {
        return parent::getItem($index);
    }

    /**
     * @inheritdoc
     */
    public function addItem(CartItemInterface $cartItem)
    {
        $cartItem->setCart($this);
        if (($index = $this->findItem($cartItem)) !== false) {
            $this->getItem($index + 1)->setQuantity($cartItem->getQuantity());
        } else {
            $this->items[] = $cartItem;
        }
        $this->calculateTotal();
    }


    /**
     * Add items
     *
     * @param $items
     */
    public function addItems($items)
    {
        $this->items[] = $items;
        $this->calculateTotal();
    }

    /**
     * Get items
     *
     * @return Doctrine\Common\Collections\Collection $items
     */
    public function getItems()
    {
        return $this->items;
    }


    public function findItem(CartItemInterface $item)
    {
        /** @var $i \Application\Vespolina\CartBundle\Document\CartItem */
        foreach ($this->getItems() as $index => $i) {
            if ($i->getCartableItem()->getId() == $item->getCartableItem()->getId()) {
                return $index;
            }
        }
        return false;
    }

    public function hasItem(CartItemInterface $cartItem)
    {
        return $this->findItem($cartItem) !== false;
    }

    protected function calculateTotal()
    {
        $subTotal = 0;
        $totalQuantity = 0;
        foreach ($this->items as $item) {
            $subTotal += $item->getPrice();
            $totalQuantity += $item->getQuantity();
        }
        // todo: extra rows like shipping and taxes
        $extraRows = 0;
        $total = $subTotal + $extraRows;
        $this->subTotal = $subTotal;
        $this->total = $total;
        $this->totalQuantity = $totalQuantity;
    }

    public function getTotalQuantity()
    {
        if (count($this->items)>0 && $this->totalQuantity==0)
        {
            $this->calculateTotal();
        }
        return $this->totalQuantity;
    }

    public function findProductQuantity(\Vespolina\CartBundle\Model\CartableItemInterface $product)
    {
        foreach($this->getItems() as $item)
        {
            if ($item->getCartableItem()->getId()==$product->getId())
            {
                return $item->getQuantity();
            }
        }
        return 0;
    }

    public function removeCartableItemById($product_id)
    {
        foreach($this->items as $index=>$item){
            if ($item->getCartableItem()->getId()==$product_id){
                $this->items->remove($index);
            }
        }
    }

    /**
     * @var int $number
     */
    protected $number;


    /**
     * Set number
     *
     * @param int $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * Get number
     *
     * @return int $number
     */
    public function getNumber()
    {
        return $this->number;
    }
    /**
     * @var float $delivery_cost
     */
    protected $delivery_cost;


    /**
     * Set delivery_cost
     *
     * @param float $deliveryCost
     */
    public function setDeliveryCost($deliveryCost)
    {
        $this->delivery_cost = $deliveryCost;
    }

    /**
     * Get delivery_cost
     *
     * @return float $deliveryCost
     */
    public function getDeliveryCost()
    {
        return $this->delivery_cost;
    }
    /**
     * @var hash $customers_data
     */
    protected $customers_data;


    /**
     * Set customers_data
     *
     * @param hash $customersData
     */
    public function setCustomersData($customersData)
    {
        $this->customers_data = $customersData;
    }

    /**
     * Get customers_data
     *
     * @return hash $customersData
     */
    public function getCustomersData()
    {
        return $this->customers_data;
    }
    /**
     * @var string $delivery_type
     */
    protected $delivery_type;


    /**
     * Set delivery_type
     *
     * @param string $deliveryType
     */
    public function setDeliveryType($deliveryType)
    {
        $this->delivery_type = $deliveryType;
    }

    /**
     * Get delivery_type
     *
     * @return string $deliveryType
     */
    public function getDeliveryType()
    {
        return $this->delivery_type;
    }
}
