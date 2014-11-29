<?php

namespace Amilio\CoreBundle\Controller;
use Amilio\CoreBundle\Form\Type\ProductType;

use Amilio\CoreBundle\Entity\Product;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductController extends Controller
{
    public function newAction($channelId)
    {
        // @todo use $channel as parameter and let sf2 do the rest
        $form = $this
                ->createForm(new ProductType(), new Product(), array('action' => $this->generateUrl('amilio_core_product_store', array("channelId" => $channelId))));

        return $this->render('AmilioCoreBundle:Product:new.html.twig', array('form' => $form->createView()));
    }

    public function storeAction(Request $request, $channelId)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this
                ->createForm(new ProductType(), new Product(), array('action' => $this->generateUrl('amilio_core_product_store', array("channelId" => $channelId))));

        $form->handleRequest($request);

        if ($form->isValid()) {

            $channel = $this->getDoctrine()->getManager()->find('AmilioCoreBundle:Channel', $channelId);

            $product = $form->getData();

            $product->addChannel($channel);

            $em->persist($product);

            $em->flush();

            return $this
                    ->redirect(
                            $this
                                    ->generateUrl('amilio_core_channel_show',
                                            array("channelName" => $channel->getCanonicalName(), "userName" => $channel->getOwner()->getUsername())));
        }

        return $this->render('AmilioCoreBundle:Product:new.html.twig', array('form' => $form->createView()));
    }

    public function showAction(Product $product, $canonicalName)
    {
        if ( $product->getCanonicalName() != $canonicalName ) {
            return $this->redirect($this->generateUrl('amilio_core_product_show', array('product' => $product->getId(), 'canonicalName' => $product->getCanonicalName())), 302);
        }
        return $this->render('AmilioCoreBundle:Product:show.html.twig', array('product' => $product, 'channels' => $product->getChannels()));
    }
}