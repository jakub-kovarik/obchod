<?php

//..............................................................................

namespace AppBundle\Utils;

//..............................................................................

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use AppBundle\Entity\Product;

//..............................................................................

class Basket
{
    // reference to the session interface
    protected $session;

    // key of the basket in the session object
    const BASKET_SESSION_KEY = 'basket';

    //..........................................................................

    // constructor
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    // get all items from the basket
    public function getItems()
    {
        $basket = $this->getBasket();
        return array_map(function($product) {
             return unserialize($product);
        }, $basket);
    }

    // get item from the basket specified by product id
    public function getItem($productId)
    {
        $basket = $this->getBasket();
        return array_key_exists($productId, $basket) ? unserialize($basket[$productId]) : null;
    }

    // add new item to the basket
    public function addItem(Product $product)
    {
        $basket = $this->getBasket();
        $basket[$product->getId()] = serialize($product);
        $this->saveBasket($basket);
    }

    // remove all items from the basket
    public function removeItems()
    {
        $this->session->remove(self::BASKET_SESSION_KEY);
    }

    // remove one item identified by product ID from the basket
    public function removeItem($productId)
    {
        $basket = $this->getBasket();
        unset($basket[$productId]);
        $this->saveBasket($basket);
    }

    //..........................................................................

    // get basket
    private function getBasket()
    {
        return $this->session->get(self::BASKET_SESSION_KEY, []);
    }

    // save basket
    private function saveBasket($basket)
    {
        $this->session->set(self::BASKET_SESSION_KEY, $basket);
    }
}

//..............................................................................
