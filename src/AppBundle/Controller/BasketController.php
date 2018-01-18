<?php

//..............................................................................

namespace AppBundle\Controller;

//..............................................................................

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Product;
use AppBundle\Utils\Basket;

//..............................................................................

class BasketController extends Controller
{
    protected $basket;

    //..........................................................................

    /**
     * Constructor.
     */
    public function __construct(Basket $basket)
    {
        $this->basket = $basket;
    }

    /**
     * Show all basket items.
     *
     * @Route("/basket", name="basket_show")
     */
    public function showAction()
    {
        // return response
        return $this->render('@App/Basket/show.html.twig', array(
            'basket' => $this->basket->getItems(),
        ));
    }

    /**
     * Add new item to the basket.
     *
     * @Route("/basket/add", name="basket_add")
     */
    public function addAction(Request $request)
    {
        // get database access instance
        $repository = $this->getDoctrine()->getRepository(Product::class);

        // find all products
        $products = $repository->findAll();

        // prepare choices which will be used in the form (product name -> product ID)
        $productFormChoices = [];
        foreach ($products as $product) {
            $productFormChoices[$product->getName()] = $product->getId();
        }

        // create the form for selecting new product and adding them to the basket
        $form = $this->createFormBuilder()
            ->add('product', ChoiceType::class, array('choices' => $productFormChoices))
            ->add('add', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        // check if the form was submitted and it is valid
        if ($form->isSubmitted() && $form->isValid())
        {
            // get the form data (key - value pairs)
            $data = $form->getData();
            $productId = $data['product'];

            // find product in the database by ID and insert it into the basket
            $this->basket->addItem($repository->findOneById($productId));

            // display message
            $this->addFlash('notice', 'Product added to the basket: product_id=' . $productId);
        }

        // return response
        return $this->render('@App/Basket/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Remove all products from the basket.
     *
     * @Route("/basket/remove", name="basket_remove_items")
     */
    public function removeAction(Request $request, SessionInterface $session)
    {
        // remove the basket
        $this->basket->removeItems();

        // redirect to the 'show' view
        return $this->redirectToRoute('basket_show');
    }
}

//..............................................................................
