<?php
namespace Amilio\CoreBundle\Controller;

use Amilio\CoreBundle\Form\Type\ChannelType;
use Amilio\CoreBundle\Entity\Channel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ChannelController extends Controller
{

    public function newAction(Request $request)
    {
        $form = $this->createForm(new ChannelType(), new Channel(), array(
            'action' => $this->generateUrl('amilio_core_channel_store')
        ));
        
        return $this->render('AmilioCoreBundle:Channel:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function storeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new ChannelType(), new Channel(), array(
            'action' => $this->generateUrl('amilio_core_channel_store')
        ));
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $channel = $form->getData();
            
            $em->persist($channel);
            $em->flush();
            
            return $this->redirect("/");
        }
        
        return $this->render('AmilioCoreBundle:Channel:new.html.twig', array(
            'form' => $form->createView()
        ));
    }
}