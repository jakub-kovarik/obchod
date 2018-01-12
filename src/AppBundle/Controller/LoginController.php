<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use AppBundle\Entity\User;
use AppBundle\Form\LoginForm;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, SessionInterface $session)
    {
        if ($session->get('login'))
            return $this->forward('AppBundle\Controller\ProductController::listAction');

        $form = $this->createForm(LoginForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)->findOneBy(['login' => $data["username"]]);
            if (!$user)
            {
                return new Response('User not exist.');
            }

            if (md5($data["password"]) != $user->getPassword())
            {
                return new Response('Failed to login.');
            }

            $session->set('login', $user->getLogin());

            return $this->forward('AppBundle\Controller\ProductController::listAction');
        }

        return $this->render('@App/login.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(SessionInterface $session)
    {
        $session->invalidate();
        return $this->forward('AppBundle\Controller\LoginController::loginAction');
    }
}
