<?php
/**
 * Created by PhpStorm.
 * User: thorben
 * Date: 22/11/14
 * Time: 15:01
 */
namespace Amilio\RetrieverBundle\ProductRetriever;

use Amilio\CoreBundle\Entity\Product;

use Pond\Tunes\Lookup;
use Pond\Tunes\Result;
use Pond\Tunes\ResultSet;

class AppleITunesRetriever implements ProductRetriever
{
    const RETRIEVER_ID = 'itunes';

    /**
     *
     * @var Lookup
     */
    private $lookup;

    public function __construct(Lookup $lookup)
    {
        $this->lookup = $lookup;
    }

    public function canHandleUrl($url)
    {
        // https://itunes.apple.com/de/album/loud-like-love-bonus-track/id679179031?l=en

        return 0 < preg_match("^(http|https)://itunes.apple.com/^", $url);
    }

    public function retrieve($url)
    {


        //$url = "http://www.amazon.de/Erfolgreiche-Softwareprojekte-Web-Gedanken-Webentwicklung-ebook/dp/B00BIUFGRI/ref=sr_1_10%05ie=UTF8&qid=1416670722&sr=8-10&keywords=langner!";
        //$url = "http://www.amazon.de/Erfolgreiche-Softwareprojekte-Web-Gedanken-Webentwicklung/dp/3868020888/ref=sr_1_1?ie=UTF8&qid=1417262130&sr=8-1&keywords=nils+langner";
        //$url = "http://www.amazon.de/gp/product/B00F3B4B8S/ref=s9_psimh_gw_p21_d21_i4?pf_rd_m=A3JWKAKR8XB7XF&pf_rd_s=center-2&pf_rd_r=11EACAJB1RXQF5WVCTC7&pf_rd_t=101&pf_rd_p=455353687&pf_rd_i=301128";

//        $pattern = '^/dp/(.*)/^';

        //https://itunes.apple.com/de/album/loud-like-love-bonus-track/id679179031?l=en

//        $pattern = '^/album/(.*)/^';
//$pattern = '(id[0-9]+)';
//++        $pattern = '/\/album\/.*\/id([0-9]+)\?/';
        $pattern = '/\.\w+\/(\w+)\/.*\/id([0-9]+)\?/';
        // $pattern = '/\/(id[0-9]+)/';
        preg_match($pattern, $url, $matches);

//print_r($matches);
//        return new Product();
        // If the url did not match our requirements we dont want to do anything
        if (empty($matches[1]) ||empty($matches[2])) {
            $pattern = '^/product/(.*)/^';

            preg_match($pattern, $url, $matches);
            if (empty($matches[1])) {
                return null;
            }
        }

        /** @var Product $product */
        $product = new Product();


        $this->lookup->setResultFormat(Lookup::RESULT_ARRAY);
        $this->lookup->setCountry($matches[1]);
        $this->lookup->setLookupId($matches[2]);
        /** @var ResultSet $resultSet */
        $resultSet = $this->lookup->request();
        //var_dump($resultSet);

        /** @var Result $result */
        $result = $resultSet->current();
/*

 var_dump($result);


object(Pond\Tunes\Result)#450 (1) {
  ["result":protected]=>
  array(19) {
    ["wrapperType"]=>
    string(10) "collection"
    ["collectionType"]=>
    string(5) "Album"
    ["artistId"]=>
    int(649817)
    ["collectionId"]=>
    int(679179031)
    ["artistName"]=>
    string(7) "Placebo"
    ["collectionName"]=>
    string(36) "Loud Like Love (Bonus Track Version)"
    ["collectionCensoredName"]=>
    string(36) "Loud Like Love (Bonus Track Version)"
    ["artistViewUrl"]=>
    string(56) "https://itunes.apple.com/de/artist/placebo/id649817?uo=4"
    ["collectionViewUrl"]=>
    string(77) "https://itunes.apple.com/de/album/loud-like-love-bonus-track/id679179031?uo=4"
    ["artworkUrl60"]=>
    string(111) "http://a4.mzstatic.com/us/r30/Music4/v4/d5/07/5a/d5075a70-fe6a-4fbd-6420-30d35067e352/13UAAIM08352.60x60-50.jpg"
    ["artworkUrl100"]=>
    string(113) "http://a1.mzstatic.com/us/r30/Music4/v4/d5/07/5a/d5075a70-fe6a-4fbd-6420-30d35067e352/13UAAIM08352.100x100-75.jpg"
    ["collectionPrice"]=>
    float(5.99)
    ["collectionExplicitness"]=>
    string(11) "notExplicit"
    ["trackCount"]=>
    int(12)
    ["copyright"]=>
    string(107) "℗ 2013 Elevator Lady Ltd., under exclusive license to Vertigo/Capitol, a division of Universal Music GmbH"
    ["country"]=>
    string(3) "DEU"
    ["currency"]=>
    string(3) "EUR"
    ["releaseDate"]=>
    string(20) "2013-01-01T08:00:00Z"
    ["primaryGenreName"]=>
    string(4) "Rock"
  }
}

*/
        $pgnAffiliateId = 'amilio-itunes-affliate-id';

        //Gather information from the product here

        /** @var Product $product */
        $product = new Product();

        //TODO this is only working for collections
        $product->setForeignId(self::RETRIEVER_ID . ':' . $result->collectionId);
        $product->setName($result->artistName . " - " . $result->collectionName);
        $product->setDescription(""); // n/a so far
        $product->setImage($result->artworkUrl100);
        $product->setImageThumbnail($result->artworkUrl60);
        $product->setManufacturer($result->artistName);
        $product->setPrice(floatval($result->collectionPrice));
        $product->setCurrency($result->currency);
        $product->setUrl($result->collectionViewUrl."&at=" . $pgnAffiliateId);

        return $product;
    }
}
