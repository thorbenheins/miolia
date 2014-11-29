<?php
namespace Amilio\RetrieverBundle\Controller;

use Amilio\CoreBundle\Entity\Product;

use Amilio\RetrieverBundle\ProductRetriever\CompositeRetriever;
use Amilio\RetrieverBundle\ProductRetriever\AmazonRetriever;
use Amilio\RetrieverBundle\ProductRetriever\ProductRetriever;
use ApaiIO\ApaiIO;
use ApaiIO\Operations\Lookup;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{

    public function getProductInfoAction(Request $request)
    {
        $url = $request->get("url");

        $compositeRetriever = new CompositeRetriever();
        $compositeRetriever->addRetriever(new AmazonRetriever($this->get("apaiio")));
        
        if ($compositeRetriever->canHandleUrl($url)) {
            //get the info from the site
            $product = $compositeRetriever->retrieve($url);
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
