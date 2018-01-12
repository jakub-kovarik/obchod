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

class BasketController extends Controller
{
    /**
     * @Route("/basket", name="basket_show")
     */
    public function showAction(SessionInterface $session)
    {
        $login = $session->get('login');
        if (!$login)
        {
            return $this->forward('AppBundle\Controller\LoginController::loginAction');
        }

        $basket = $session->get('basket');

        return $this->render('@App/Basket/show.html.twig', array(
            'basket' => $basket,
            'login' => $login,
        ));
    }

    /**
     * @Route("/basket/add", name="basket_add")
     */
    public function addAction(Request $request, SessionInterface $session)
    {
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $products   = $repository->findAll();

        $productFormChoices = array();
        foreach ($products as $product)
        {
            array_push($productFormChoices, array($product->getName() => $product->getId()));
        }

        $form = $this->createFormBuilder()
            ->add('product', ChoiceType::class, array('choices' => $productFormChoices))
            ->add('add', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $productId = $data['product'];

            $basket = $session->get('basket');
            if (!$basket)
            {
                $basket = array();
            }
            $basket[$productId] = (array_key_exists($productId, $basket)) ? $basket[$productId] + 1 : 1;

            $session->set('basket', $basket);
        }

        return $this->render('@App/Basket/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
