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
	foreach( $channels as $channel ) {
		$channelString .= $channel->getId() . "-"; 
	}

	$response->headers->setCookie(new Cookie("favs", $channelString, 0, '/', null, false, false));	

	$response->send();

        // @todo this route should be configured
        return $this->redirect($this->generateUrl('amilio_core_user_index'));
    }
    
    public function postLogoutAction()
    {
        $response = new Response();
        $response->headers->setCookie(new Cookie("userId", '', 0, '/', null, false, false));
        $response->headers->setCookie(new Cookie("userName", '', 0, '/', null, false, false));
        $response->headers->setCookie(new Cookie("favs", '', 0, '/', null, false, false));
	$response->send();
    
        // @todo this route should be configured
        return $this->redirect($this->generateUrl('amilio_core_homepage'));
    }
}
