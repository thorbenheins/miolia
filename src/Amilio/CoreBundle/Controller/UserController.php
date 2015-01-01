<?php

namespace Amilio\CoreBundle\Controller;

use Amilio\CoreBundle\Entity\User;

use Symfony\Component\HttpFoundation\Cookie;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * @todo sollte diese action vielleicht in einem anderen bundle liegen?
     */
    public function indexAction($index)
    {
        $elementsPerPage = 30;
        $favs = $this->getDoctrine()->getRepository('AmilioCoreBundle:ChannelElement')->findFavouritesByUser($this->getUser(), ($index - 1) * $elementsPerPage, $elementsPerPage);
        return $this->render('AmilioCoreBundle:User:index.html.twig', array('favs' => $favs));
    }

    public function showChannelsAction($username) {
        $user = $this->getDoctrine()->getRepository('AmilioCoreBundle:User')->findOneByUsernameCanonical($username);

        if( is_null($user)) {
             throw $this->createNotFoundException('This User does not exist.');
        }

        $channels = $user->getChannels();
        return $this->render('AmilioCoreBundle:User:channels.html.twig', array('channels' => $channels, 'user' => $user));
    }
}
