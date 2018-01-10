<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductForm;


class ProductController extends Controller
{
    /**
     * @Route("/products")
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products = $repository->findAll();

        return $this->render('@App/Product/list.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     * @Route("/products/add")
     */
    public function addAction(Request $request)
    {
        $product = new Product();

        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $product = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return new Response('Saved new product with id '.$product->getId());
        }

        return $this->render('@App/Product/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/products/{productId}/edit")
     */
    public function editAction(Request $request, $productId)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->findOneById($productId);
        if (!$product)
            throw $this->createNotFoundException('No product found for id '.$productId);

        $form = $this->createForm(ProductForm::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $product = $form->getData();

            $em->persist($product);
            $em->flush();

            return new Response('Updated product with id '.$product->getId());
        }
        else
            $form->setData($product);

        return $this->render('@App/Product/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
