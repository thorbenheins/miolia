<?php

namespace Amilio\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $newest = $this->getDoctrine()->getRepository('AmilioCoreBundle:ChannelElement')->findBy(array(), array('id' => 'DESC'), 10, 0);

        $homepageChannel = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->find(30);
        
	return $this->render('AmilioCoreBundle:Default:index.html.twig', array(
            'newest' => $newest, 'homepageChannel' => $homepageChannel
        ));
    }

    public function showStaticAction($blogurl)
    {
	$url = "http://blog.amilio.de/" . $blogurl . "/";
	$content = file_get_contents($url);
        return $this->render('AmilioCoreBundle:Default:static.html.twig', array('content' => $content
        ));
    }   
}
