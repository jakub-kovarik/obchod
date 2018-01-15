<?php

namespace AppBundle;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use AppBundle\Entity\Product;

class Basket
{
    protected $session;
    const BASKET_SESSION_KEY = 'basket';

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getItems()
    {
        $basket = $this->session->get(self::BASKET_SESSION_KEY, []);
        return array_map(function($product) {
             return unserialize($product);
        }, $basket);
    }

    public function addItem(Product $product)
    {
        $basket = $this->session->get(self::BASKET_SESSION_KEY, []);
        $basket[$product->getId()] = serialize($product);
        $this->session->set(self::BASKET_SESSION_KEY, $basket);
    }

    public function removeItems()
    {
        $this->session->remove(self::BASKET_SESSION_KEY);
    }

    public function removeItem($productId)
    {
        return getItems()[$productId];
    }
}
