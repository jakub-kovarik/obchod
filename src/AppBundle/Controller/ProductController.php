<?php

//..............................................................................

namespace AppBundle\Controller;

//..............................................................................

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductForm;

//..............................................................................

class ProductController extends Controller
{
    /**
     * Show all products.
     *
     * @Route("/products, name="products_show")
     */
    public function listAction()
    {
        // get database access instance
        $repository = $this->getDoctrine()->getRepository(Product::class);

        // find all products
        $products = $repository->findAll();

        // return response
        return $this->render('@App/Product/list.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     *
     * Show product.
     *
     * @Route("/products/{productId}", name="product_show", requirements={"productId"="\d+"})
     */
    public function showAction($productId)
    {
        // get database access
        $repository = $this->getDoctrine()->getRepository(Product::class);

        // find product by product ID
        $product = $repository->findOneById($productId);

        // return response
        return $this->render('@App/Product/show.html.twig', array(
            'product' => $product,
        ));
    }

    /**
     * Add new product.
     *
     * @Route("/products/add", name="product_add")
     */
    public function addAction(Request $request)
    {
        // create new product
        $product = new Product();

        // create the form for creating new product
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        // check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid())
        {
            // get the form data (key - value pairs)
            $product = $form->getData();

            // save new product to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            // display message
            $this->addFlash('notice', 'New product saved: product_id='.$product->getId());
        }

        // return response
        return $this->render('@App/Product/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Edit existing product.
     *
     * @Route("/products/{productId}/edit", name="product_edit")
     */
    public function editAction(Request $request, $productId)
    {
        // get database access instance
        $em = $this->getDoctrine()->getManager();

        // find product by ID
        $product = $em->getRepository(Product::class)->findOneById($productId);
        if (!$product)
        {
            // display message
            $this->addFlash('notice', 'Category not found for slug: slug='.$slug);
        }

        // create the form for creating new product
        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        // check if the form is submitted
        if ($form->isSubmitted())
        {
            // and valid
            if ($form->isValid())
            {
                // get the form data (key - pair) values
                $product = $form->getData();

                // and save it to the database
                $em->persist($product);
                $em->flush();

                // display message
                $this->addFlash('notice', 'Product updated: product_id='.$product->getId());
            }
        }
        else
        {
            // set existing data in the form inputs
            $form->setData($product);
        }

        // return response
        return $this->render('@App/Product/edit.html.twig', array(
            'product_id' => $productId,
            'form' => $form->createView(),
        ));
    }
}

//..............................................................................
