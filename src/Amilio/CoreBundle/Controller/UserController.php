<?php

namespace Amilio\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function indexAction($index)
    {
	$elementsPerPage = 30;

	$favs = $this->getDoctrine()->getRepository('AmilioCoreBundle:ChannelElement')->findFavouritesByUser($this->getUser(), ($index - 1) * $elementsPerPage, $elementsPerPage);

        return $this->render('AmilioCoreBundle:User:index.html.twig', array('favs' => $favs));
    }
}
