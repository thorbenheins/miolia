<?php

namespace Amilio\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        // $newest = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->findBy(array(), array('id' => 'DESC'), 20, 0);
        $channelsOfTheWeek = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->findChannelsOfTheWeek();
        
        $newest = $this->getDoctrine()->getRepository('AmilioCoreBundle:ChannelElement')->findBy(array(), array('id' => 'DESC'), 10, 0);
        
        return $this->render('AmilioCoreBundle:Default:index.html.twig', array(
            'newest' => $newest, 'channelsOfTheWeek' => $channelsOfTheWeek
        ));
    }   
}
