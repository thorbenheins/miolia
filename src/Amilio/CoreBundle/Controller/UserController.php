<?php

namespace Amilio\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{

    public function postLoginAction()
    {
        $response = new Response();
        $response->headers->setCookie(new Cookie("userId", $this->getUser()->getId(), 0, '/', null, false, false));
        $response->send();

        return $this->redirect($this->generateUrl('amilio_core_user_index'));
    }
    
    public function postLogoutAction()
    {
        $response = new Response();
        $response->headers->setCookie(new Cookie("userId", '', 0, '/', null, false, false));
        $response->send();
    
        return $this->redirect($this->generateUrl('amilio_core_homepage'));
    }
    
    public function indexAction()
    {
        return $this->render('AmilioCoreBundle:User:index.html.twig');
    }
}
