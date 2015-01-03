<?php

namespace Amilio\SitemapBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $response = $this->render('AmilioSitemapBundle:Default:index.xml.twig');
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        return $response;
    }

    public function productAction()
    {
        $products = $this->getDoctrine()->getRepository('AmilioCoreBundle:Product')->findAll();
        $response = $this->render('AmilioSitemapBundle:Default:productSitemap.xml.twig', array('products' => $products));

        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        return $response;
    }

    public function channelAction()
    {
        $channels = $this->getDoctrine()->getRepository('AmilioCoreBundle:Channel')->findAll();
        $response = $this->render('AmilioSitemapBundle:Default:channelSitemap.xml.twig', array('channels' => $channels));

        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        return $response;
    }
}