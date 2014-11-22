<?php

namespace Amilio\ShopBundle\Controller;

use Amilio\ShopBundle\ProductRetriever\AmazonRetriever;
use Amilio\ShopBundle\ProductRetriever\ProductRetriever;
use ApaiIO\ApaiIO;
use ApaiIO\Operations\Lookup;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function getProductInfoAction($productUrl)
    {
        $url = base64_decode($productUrl);

        /** @var ProductRetriever $retriever */
        $retriever = new AmazonRetriever($this->get("apaiio"));

        //get the info from the site
        $retriever->retrieve($url);

        return $this->render('AmilioShopBundle:Default:index.html.twig', array('name' => $url));
    }
}
