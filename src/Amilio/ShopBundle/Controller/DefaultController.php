<?php
namespace Amilio\ShopBundle\Controller;

use Amilio\CoreBundle\Entity\Product;

use Amilio\ShopBundle\ProductRetriever\CompositeRetriever;
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
        
        $compositeRetriever = new CompositeRetriever();
        $compositeRetriever->addRetriever(new AmazonRetriever($this->get("apaiio")));
        
        if ($compositeRetriever->canHandleUrl($url)) {
            //get the info from the site
            $product = $compositeRetriever->retrieve($url);
        } else {
            $product = new Product();
        }

        return $this->render(
            'AmilioShopBundle:JSON:productInfo.json.twig',
            array(
                'data' => $product->jsonSerialize()
            )
        );
    }
}
