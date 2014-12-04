<?php

namespace Amilio\CoreBundle\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use Amilio\CoreBundle\Entity\Channel;

use Amilio\CoreBundle\Form\Type\ProductType;

use Amilio\CoreBundle\Entity\Product;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductController extends Controller
{
    public function newAction($channelId)
    {
        $form = $this->createForm(new ProductType(), new Product(), array('action' => $this->generateUrl('amilio_core_product_store', array("channelId" => $channelId))));

        return $this->render('AmilioCoreBundle:Product:new.html.twig', array('form' => $form->createView()));
    }

    public function storeAction(Request $request, $channelId)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new ProductType(), new Product(), array('action' => $this->generateUrl('amilio_core_product_store', array("channelId" => $channelId))));

        $form->handleRequest($request);

        if ($form->isValid()) {

            $channel = $this->getDoctrine()->getManager()->find('AmilioCoreBundle:Channel', $channelId);
            $product = $form->getData();
            $product->addChannel($channel);
            $em->persist($product);
            $em->flush();

            return $this->redirect($this->generateUrl('amilio_core_channel_show', array("channel" => $channel->getId(),  "canonicalName" => $channel->getCanonicalName())));
        }

        return $this->render('AmilioCoreBundle:Product:new.html.twig', array('form' => $form->createView()));
    }

    public function showAction(Product $product, $canonicalName)
    {
        if ($product->getCanonicalName() != $canonicalName) {
            return $this->redirect($this->generateUrl('amilio_core_product_show', array('product' => $product->getId(), 'canonicalName' => $product->getCanonicalName())), 302);
        }
        return $this->render('AmilioCoreBundle:Product:show.html.twig', array('product' => $product, 'channels' => $product->getChannels()));
    }

    public function shareAction(Product $product)
    {
        return $this->render('AmilioCoreBundle:Product:share.html.twig', array('product' => $product, 'channels' => $this->getUser()->getChannels()));
    }
    
    public function shareStoreAction(Request $request)
    {
        $em =  $this->getDoctrine()->getManager();

        $product = $em->find('AmilioCoreBundle:Product', $request->get('productId'));
        $channel = $em->find('AmilioCoreBundle:Channel', $request->get('channelId'));
        
        $product->addChannel($channel);
        
        $em->persist($product);
        $em->flush();
        
        return $this->redirect($this->generateUrl('amilio_core_channel_show', array('canonicalName' => $channel->getCanonicalName(), 'channel' => $channel->getId() )));
    }  

    public function removeAction(Channel $channel, Product $product) 
    {
        if( $this->getUser()->getId() != $channel->getOwner()->getId() ) {
            throw new AccessDeniedHttpException();
        }
        
        $channel->removeProduct($product);
        $em =  $this->getDoctrine()->getManager();
        
        $em->persist($channel);
        $em->flush();  
        
        return $this->redirect($this->generateUrl('amilio_core_channel_show', array('canonicalName' => $channel->getCanonicalName(), 'channel' => $channel->getId() )));
    }  
}
