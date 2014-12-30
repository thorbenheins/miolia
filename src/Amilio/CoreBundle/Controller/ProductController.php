<?php

namespace Amilio\CoreBundle\Controller;
use Amilio\CoreBundle\Entity\ChannelElement;

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
        $product = new Product();
        $form = $this->createForm(new ProductType(), $product, array('action' => $this->generateUrl('amilio_core_product_store', array("channel" => $channelId))));
        return $this->render('AmilioCoreBundle:Product:new.html.twig', array('product' => $product, 'form' => $form->createView()));
    }

    public function storeAction(Request $request, Channel $channel, $productId)
    {
        if ($productId == -1) {
            $product = new Product();
        } else {
            $product = $this->getDoctrine()->getRepository("AmilioCoreBundle:Product")->find($productId);
        }


        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(new ProductType(), $product, array('action' => $this->generateUrl('amilio_core_product_store', array("channel" => $channel->getId(), 'productId' => $productId))));

        $form->handleRequest($request);

        // @todo check if the user is allowed to add the product

        if ($form->isValid()) {

            $product = $form->getData();

            if ($productId == -1) {
                $element = new ChannelElement();
                $product->setOwner($this->getUser());
            }

            $em->persist($product);
            $em->flush();

            if ($productId == -1) {
                $element->setElement($product);
                $element->setChannel($channel);
                $em->persist($element);

                $channel->addElement($element);
                $em->persist($channel);

                $em->flush();
            }

            return $this->redirect($this->generateUrl('amilio_core_channel_show', array("channel" => $channel->getId(), "canonicalName" => $channel->getCanonicalName())));
        }
        return $this->render('AmilioCoreBundle:Product:new.html.twig', array('form' => $form->createView()));
    }

    public function showAction(Request $request, Product $product, $canonicalName)
    {

        if ($product->getCanonicalName() != $canonicalName) {
            return $this->redirect($this->generateUrl('amilio_core_product_show', array('product' => $product->getId(), 'canonicalName' => $product->getCanonicalName())), 302);
        }

        $containingChannels = $this->getDoctrine()->getRepository("AmilioCoreBundle:Channel")->findByProduct($product);

        if ($request->isXmlHttpRequest()) {
            $template = 'AmilioCoreBundle:Product:show.modal.html.twig';
        } else {
            $template = 'AmilioCoreBundle:Product:show.html.twig';
        }

        return $this->render($template, array('product' => $product, 'containingChannels' => $containingChannels));
    }

    public function shareAction(Product $product)
    {
        return $this->render('AmilioCoreBundle:Product:share.html.twig', array('product' => $product, 'channels' => $this->getUser()->getChannels()));
    }

    public function shareStoreAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // @todo check if post reuqest
        // @todo check if channel is owned by this->getUser()

        $product = $em->find('AmilioCoreBundle:Product', $request->get('productId'));
        $channel = $em->find('AmilioCoreBundle:Channel', $request->get('channelId'));

        $element = new ChannelElement();

        $element->setElement($product);
        $element->setChannel($channel);

        $em->persist($element);
        $em->flush();

        return $this->redirect($this->generateUrl('amilio_core_channel_show', array('canonicalName' => $channel->getCanonicalName(), 'channel' => $channel->getId())));
    }

    public function removeAction(Request $request, ChannelElement $element)
    {
        $channel = $element->getChannel();

        if ($this->getUser()->getId() != $channel->getOwner()->getId() || $request->getMethod() != "POST") {
            throw new AccessDeniedHttpException();
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($element);
        $em->flush();

        return $this->redirect($this->generateUrl('amilio_core_channel_show', array('canonicalName' => $channel->getCanonicalName(), 'channel' => $channel->getId())));
    }

    public function editAction(Product $product, Channel $channel)
    {
        $form = $this->createForm(new ProductType(), $product, array('action' => $this->generateUrl('amilio_core_product_store', array("channel" => $channel->getId(), 'productId' => $product->getId()))));
        return $this->render('AmilioCoreBundle:Product:new.html.twig', array('product' => $product, 'form' => $form->createView()));
    }
}
