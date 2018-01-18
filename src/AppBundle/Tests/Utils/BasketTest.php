<?php

//..............................................................................

namespace Tests\Utils;

//..............................................................................

use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Session;

use AppBundle\Utils\Basket;
use AppBundle\Entity\Product;
use PHPUnit\Framework\TestCase;

//..............................................................................

class BasketTest extends TestCase
{
    /**
     * Test - getting one item from the basket.
     */
    public function testGetItem()
    {
        // create basket
        $basket = $this->createBasket();

        // prepare input test data
        $input = $this->prepareInput();

        // for each item in the input data array
        foreach($input as $product)
        {
            // save input test data to the basket
            $basket->addItem($product);
        }

        // get data from the input
        foreach($input as $product)
        {
            // and compare it with the basket
            $this->assertEquals($product, $basket->getItem($product->getId()));
        }
    }

    /**
     * Test - adding items to the basket.
     */
    public function testAddItem()
    {
        // create basket
        $basket = $this->createBasket();

        // prepare input test data
        $input = $this->prepareInput();

        // for each item in the input data array
        foreach($input as $product)
        {
            // save input test data to the basket
            $basket->addItem($product);
        }

        // get data from the basket
        $output = $basket->getItems();

        // and compare it with the input
        $this->assertEquals($input, $output);
    }

    /**
     * Test - removing items from the basket.
     */
    public function testRemoveItems()
    {
        // create basket
        $basket = $this->createBasket();

        // prepare input test data
        $input = $this->prepareInput();

        // for each item in the input data array
        foreach($input as $product)
        {
            // save input test data to the basket
            $basket->addItem($product);
        }

        // check the number of items in the basket
        $this->assertNotEquals(0, count($basket->getItems()));

        // remove items from the basket
        $basket->removeItems();

        // check the number of items in the basket
        $this->assertEquals(0, count($basket->getItems()));
    }

    /**
     * Test - removing item from the basket.
     */
    public function testRemoveItem()
    {
        // create basket
        $basket = $this->createBasket();

        // prepare input test data
        $input = $this->prepareInput();

        // for each item in the input data array
        foreach($input as $product)
        {
            // save input test data to the basket
            $basket->addItem($product);
        }

        foreach($input as $product)
        {
            // check if item is in the basket
            $this->assertEquals($product, $basket->getItem($product->getId()));

            // remove item from the basket
            $basket->removeItem($product->getId());

            // check if item is not in the basket
            $this->assertEquals(null, $basket->getItem($product->getId()));
        }
    }

    //..........................................................................

    /**
     *  Create empty basket.
     */
    private function createBasket()
    {
        // create session for this test
        $session = new Session(new MockArraySessionStorage());

        // create and return basket
        return new Basket($session);
    }

    /**
     * Prepare data used as an input for testing.
     */
    private function prepareInput()
    {
        // create input test data
        $input = array();

        // fill input with sample values
        for($i = 0; $i < 10; $i++)
        {
            $product = new Product();
            $product->setId($i);
            $product->setName('product name: idx=' . $i);
            $product->setPrice(100 + $i);
            $product->setDescription('product description: idx=' . $i);

            array_push($input, $product);
        }

        // return prepared input test data
        return $input;
    }
}

//..............................................................................
