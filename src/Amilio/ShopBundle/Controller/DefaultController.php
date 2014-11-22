<?php

namespace Amilio\ShopBundle\Controller;

use Amilio\ShopBundle\ProductRetriever\AmazonRetriever;
use Amilio\ShopBundle\ProductRetriever\ProductRetriever;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function getProductInfoAction($productUrl)
    {

        $url = base64_decode($productUrl);

        //check if it is amazon

        /** @var ProductRetriever $retriever */
        $retriever = new AmazonRetriever();


        //get the info from the site

        //

        return $this->render('AmilioShopBundle:Default:index.html.twig', array('name' => $url));
    }
}
