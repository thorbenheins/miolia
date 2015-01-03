<?php

namespace Amilio\UserBundle\Controller;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function postLoginAction()
    {
        $response = new Response();
        $response->headers->setCookie(new Cookie("userId", $this->getUser()->getId(), 0, '/', null, false, false));
        $response->headers->setCookie(new Cookie("userName", $this->getUser()->getUsername(), 0, '/', null, false, false));

        $user = $this->getUser();
        $channels = $user->getFavouriteChannels();

        $channelString = "-0-";
        foreach ($channels as $channel) {
            $channelString .= $channel->getId() . "-";
        }

        $response->headers->setCookie(new Cookie("favs", $channelString, 0, '/', null, false, false));

        $response->send();
        // @todo this route should be configured
        return $this->render('AmilioUserBundle:Default:closemodal.html.twig', array('url' => $this->generateUrl('amilio_core_user_index', array('start' => 0))));
    }

    private function clearCookies()
    {
        $response = new Response();
        $response->headers->setCookie(new Cookie("userId", '', 0, '/', null, false, false));
        $response->headers->setCookie(new Cookie("userName", '', 0, '/', null, false, false));
        $response->headers->setCookie(new Cookie("favs", '', 0, '/', null, false, false));
        $response->send();
    }

    public function postLogoutAction()
    {
        $this->clearCookies();

        // @todo this route should be configured
        return $this->redirect($this->generateUrl('amilio_core_homepage'));
    }

    public function showLoginAction()
    {
        $this->clearCookies();
        return $this->redirect($this->generateUrl('amilio_user_notallowed'));
    }

    public function notAllowedAction()
    {
        $response = $this->render('AmilioUserBundle:Default:notAllowed.html.twig');
        $response->setStatusCode(403);
        return $response;
    }

    public function checkEmailAction()
    {
        return $this->render('AmilioUserBundle:Default:checkEmail.html.twig');
    }
}