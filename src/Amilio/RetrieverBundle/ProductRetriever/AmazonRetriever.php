<?php
/**
 * Created by PhpStorm.
 * User: thorben
 * Date: 22/11/14
 * Time: 15:01
 */
namespace Amilio\RetrieverBundle\ProductRetriever;

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
        //$url = "http://www.amazon.de/Erfolgreiche-Softwareprojekte-Web-Gedanken-Webentwicklung-ebook/dp/B00BIUFGRI/ref=sr_1_10%05ie=UTF8&qid=1416670722&sr=8-10&keywords=langner!";
        //$url = "http://www.amazon.de/Erfolgreiche-Softwareprojekte-Web-Gedanken-Webentwicklung/dp/3868020888/ref=sr_1_1?ie=UTF8&qid=1417262130&sr=8-1&keywords=nils+langner";
        //$url = "http://www.amazon.de/gp/product/B00F3B4B8S/ref=s9_psimh_gw_p21_d21_i4?pf_rd_m=A3JWKAKR8XB7XF&pf_rd_s=center-2&pf_rd_r=11EACAJB1RXQF5WVCTC7&pf_rd_t=101&pf_rd_p=455353687&pf_rd_i=301128";

        $pattern = '^/dp/(.*)/^';
        
        preg_match($pattern, $url, $matches);

        // If the url did not match our requirements we dont want to do anything
        if (empty($matches[1])) {
            $pattern = '^/product/(.*)/^';

            preg_match($pattern, $url, $matches);
            if (empty($matches[1])) {
                return null;
            }
        }

        $lookup = new Lookup();

        $lookup->setItemId($matches[1]);
        $lookup->setResponseGroup(array('Large'));
        $formattedResponse = $this->apaiIO->runOperation($lookup);

        $doc = new \DOMDocument("1.0", "UTF-8");
        $doc->loadXML($formattedResponse);

        //file_put_contents("/tmp/amazon.xml", $doc->saveXML());

        /** @var \DOMXPath $xpath */
        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace("a", "http://webservices.amazon.com/AWSECommerceService/2011-08-01");

        //Gather information from the product here

        /** @var Product $product */
        $product = new Product();

        $query = "//a:Item/a:ASIN";
        $nodes = $xpath->query($query);
        if ($nodes->length > 0) {
            $product->setForeignId($nodes->item(0)->nodeValue);
        }
        $query = "//a:ItemAttributes/a:Title";
        $nodes = $xpath->query($query);
        if ($nodes->length > 0) {
            $product->setName($nodes->item(0)->nodeValue);
        }

        $query = "//a:EditorialReview/a:Content";
        $nodes = $xpath->query($query);
        if ($nodes->length > 0) {
            $product->setDescription($nodes->item(0)->nodeValue);
        }

        $query = "//a:ImageSet[@Category='primary']/a:LargeImage/a:URL";
        $nodes = $xpath->query($query);
        if ($nodes->length > 0) {
            $product->setImage($nodes->item(0)->nodeValue);
        }

        $query = "//a:ImageSet[@Category='primary']/a:ThumbnailImage/a:URL";
        $nodes = $xpath->query($query);
        if ($nodes->length > 0) {
            $product->setImageThumbnail($nodes->item(0)->nodeValue);
        }

        //TODO: The image dimensions might be interesting too for css styles etc.
        //

        $query = "//a:ItemAttributes/a:Manufacturer";
        $nodes = $xpath->query($query);
        if ($nodes->length > 0) {
            $product->setManufacturer($nodes->item(0)->nodeValue);
        }

        //<Amount>2490</Amount>
        $query = "//a:OfferSummary/a:LowestNewPrice/a:Amount";
        $nodes = $xpath->query($query);
        if ($nodes->length > 0) {
            $stringIntVal = $nodes->item(0)->nodeValue;

            $stringIntVal = substr($stringIntVal,0,strlen($stringIntVal)-2) . "." . substr($stringIntVal,strlen($stringIntVal)-2);

            $product->setPrice(floatval($stringIntVal));
        }

        //CurrencyCode
        $query = "//a:OfferSummary/a:LowestNewPrice/a:CurrencyCode";
        $nodes = $xpath->query($query);
        if ($nodes->length > 0) {
            $product->setCurrency($nodes->item(0)->nodeValue);
        }

        $query = "//a:DetailPageURL";
        $nodes = $xpath->query($query);
        if ($nodes->length > 0) {
            $product->setUrl($nodes->item(0)->nodeValue);
        }

        //TODO: This attribute might be of interest: IsAdultProduct


        return $product;
    }
}
