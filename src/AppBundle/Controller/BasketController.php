<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use AppBundle\Entity\Product;
use AppBundle\Util\Basket;

class BasketController extends Controller
{
    protected $basket;

    public function __construct(Basket $basket)
    {
        $this->basket = $basket;
    }

    /**
     * @Route("/basket", name="basket_show")
     */
    public function showAction()
    {
        return $this->render('@App/Basket/show.html.twig', array(
            'basket' => $this->basket->getItems(),
        ));
    }

    /**
     * @Route("/basket/add", name="basket_add")
     */
    public function addAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products   = $repository->findAll();

        $productFormChoices = [];
        foreach ($products as $product) {
            $productFormChoices[$product->getName()] = $product->getId();
        }

        $form = $this->createFormBuilder()
            ->add('product', ChoiceType::class, array('choices' => $productFormChoices))
            ->add('add', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $this->basket->addItem($repository->findOneById($data['product']));
        }

        return $this->render('@App/Basket/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

     /**
     * @Route("/basket/remove", name="basket_remove_items")
     */
    public function removeAction(Request $request, SessionInterface $session)
    {
        $this->basket->removeItems();
        return $this->redirectToRoute('basket_show');
    }
}
