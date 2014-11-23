<?php
/**
 * Created by PhpStorm.
 * User: thorben
 * Date: 22/11/14
 * Time: 15:01
 */
namespace Amilio\ShopBundle\ProductRetriever;

use Amilio\CoreBundle\Entity\Product;
use ApaiIO\ApaiIO;
use ApaiIO\Operations\Lookup;

class AmazonRetriever implements ProductRetriever
{

    /**
     *
     * @var ApaiIO
     */
    private $apaiIO;

    public function __construct(ApaiIO $apaiIO)
    {
        $this->apaiIO = $apaiIO;
    }

    public function canHandleUrl($url)
    {
        return 0 < preg_match("^(http|https)://www.amazon.de/^", $url); 
    }

    public function retrieve($url)
    {
        $url = "http://www.amazon.de/Erfolgreiche-Softwareprojekte-Web-Gedanken-Webentwicklung-ebook/dp/B00BIUFGRI/ref=sr_1_10%05ie=UTF8&qid=1416670722&sr=8-10&keywords=langner!";
        
        $pattern = '^/dp/(.*)/^';
        
        preg_match($pattern, $url, $matches);
        
        // If the url did not match our requirements we dont want to do anything
        if (empty($matches[1])) {
            return null;
        }

        $lookup = new Lookup();

        $lookup->setItemId($matches[1]);
        $lookup->setResponseGroup(array('Large', 'Small'));
        $formattedResponse = $this->apaiIO->runOperation($lookup);

        $doc = new \DOMDocument("1.0", "UTF-8");
        $doc->loadXML($formattedResponse);

        /** @var \DOMXPath $xpath */
        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace("a", "http://webservices.amazon.com/AWSECommerceService/2011-08-01");

        //Gather information from the product here

        /** @var Product $product */
        $product = new Product();

        $query = "//a:Item/a:ASIN";
        $nodes = $xpath->query($query);
        $product->setForeignId($nodes->item(0)->nodeValue);

        $query = "//a:ItemAttributes/a:Title";
        $nodes = $xpath->query($query);
        $product->setName($nodes->item(0)->nodeValue);

        $query = "//a:EditorialReview/a:Content";
        $nodes = $xpath->query($query);
        $product->setDescription($nodes->item(0)->nodeValue);

        $query = "//a:ImageSet[@Category='primary']/a:LargeImage/a:URL";
        $nodes = $xpath->query($query);
        $product->setImage($nodes->item(0)->nodeValue);

        //TODO: The image dimensions might be interesting too for css styles etc.
        //

        $query = "//a:ItemAttributes/a:Manufacturer";
        $nodes = $xpath->query($query);
        $product->setManufacturer($nodes->item(0)->nodeValue);

        //hmmm price is not in the response...
        $product->setPrice(null);

        $query = "//a:DetailPageURL";
        $nodes = $xpath->query($query);
        $product->setUrl($nodes->item(0)->nodeValue);

        //TODO: This attribute might be of interest: IsAdultProduct

        return $product;
    }
}
