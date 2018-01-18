<?php

//..............................................................................

namespace AppBundle\Controller;

//..............................................................................

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryForm;

//..............................................................................

class CategoryController extends Controller
{
    /**
     * Show all categories.
     *
     * @Route("/categories, name="categories_show")
     */
    public function listAction()
    {
        // get database access instance
        $repository = $this->getDoctrine()->getRepository(Category::class);

        // find all categories
        $categories = $repository->findAll();

        // return response
        return $this->render('@App/Category/list.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * Add new category.
     *
     * @Route("/categories/add", name="category_add")
     */
    public function addAction(Request $request)
    {
        // create empty category
        $category = new Category();

        // create the form for creating new category
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        // check if the form was submitted and it is valid
        if ($form->isSubmitted() && $form->isValid())
        {
            // get the form data (key - value pairs)
            $category = $form->getData();

            // save new category to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            // display message
            $this->addFlash('notice', 'New category saved: category_id='.$category->getId());
        }

        // return response
        return $this->render('@App/Category/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Show category.
     *
     * @Route("/categories/{slug}", name="category_show")
     */
    public function showAction($slug)
    {
        // get database access instance
        $repository = $this->getDoctrine()->getRepository(Category::class);

        // find category by slug
        $category = $repository->findOneBySlug($slug);

        // return response
        return $this->render('@App/Category/show.html.twig', array(
            'category' => $category,
        ));
    }

    /**
     * Edit existing category.
     *
     * @Route("/categories/{slug}/edit", name="category_edit")
     */
    public function editAction(Request $request, $slug)
    {
        // get database access instance
        $em = $this->getDoctrine()->getManager();

        // find category by slug
        $category = $em->getRepository(Category::class)->findOneBySlug($slug);
        if (!$category)
        {
            // display message
            $this->addFlash('notice', 'Category not found for slug: slug='.$slug);
        }

        // get the form data (key - value pairs)
        $form = $this->createForm(CategoryForm::class, $category);
        $form->handleRequest($request);

        // check if the form was submitted
        if ($form->isSubmitted())
        {
            // and it is valid
            if ($form->isValid())
            {
                // get the form data (key - pair) values
                $category = $form->getData();

                // and save it to the database
                $em->persist($category);
                $em->flush();

                // display message
                $this->addFlash('notice', 'Category updated: category_id='.$category->getId());
            }
        }
        else
        {
            // set existing data in the form inputs
            $form->setData($category);
        }

        // return response
        return $this->render('@App/Category/edit.html.twig', array(
            'category_id' => $category->getId(),
            'form' => $form->createView(),
        ));
    }
}

//..............................................................................
