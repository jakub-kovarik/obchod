<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryForm;

class CategoryController extends Controller
{
    /**
     * @Route("/categories")
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $repository->findAll();

        return $this->render('@App/Category/list.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * @Route("/categories/add", name="category_add")
     */
    public function addAction(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $category = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return new Response('Saved new category with id '.$category->getId());
        }

        return $this->render('@App/Category/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/categories/{slug}", name="category_show")
     */
    public function showAction($slug)
    {
        $repository = $this->getDoctrine()->getRepository(Category::class);
        $category   = $repository->findOneBySlug($slug);

        return $this->render('@App/Category/show.html.twig', array(
            'category' => $category,
        ));
    }

    /**
     * @Route("/categories/{slug}/edit", name="category_edit")
     */
    public function editAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository(Category::class)->findOneBySlug($slug);
        if (!$category)
        {
            return new Response('No category found for slug '.$slug);
        }

        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            if ($form->isValid())
            {
                $category = $form->getData();

                $em->persist($category);
                $em->flush();

                return new Response('Updated category with id '.$category->getId());
            }
        }
        else
        {
            $form->setData($category);
        }

        return $this->render('@App/Category/edit.html.twig', array(
            'category_id' => $category->getId(),
            'form' => $form->createView(),
        ));
    }
}
