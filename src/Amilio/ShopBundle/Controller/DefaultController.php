<?php
namespace Amilio\ShopBundle\Controller;

use Amilio\CoreBundle\Entity\Product;

use Amilio\ShopBundle\ProductRetriever\CompositeRetriever;
use Amilio\ShopBundle\ProductRetriever\AmazonRetriever;
use Amilio\ShopBundle\ProductRetriever\ProductRetriever;
use ApaiIO\ApaiIO;
use ApaiIO\Operations\Lookup;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function getProductInfoAction($productUrl)
    {
        $url = base64_decode($productUrl);
        
        $compositeRetriever = new CompositeRetriever();
        $compositeRetriever->addRetriever(new AmazonRetriever($this->get("apaiio")));
        
        if ($compositeRetriever->canHandleUrl($url)) {
            //get the info from the site
            $product = $compositeRetriever->retrieve($url);
        //    $em = $this->getDoctrine()->getManager();
        //    $em->persist($product);
        //    $em->flush();
        } else {
            $product = new Product();
        }
        /** @var JsonResponse $response */
        $response = new JsonResponse();

        $response->setData($product->jsonSerialize());
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
