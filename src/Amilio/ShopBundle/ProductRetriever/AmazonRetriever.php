<?php
/**
 * Created by PhpStorm.
 * User: thorben
 * Date: 22/11/14
 * Time: 15:01
 */

namespace Amilio\ShopBundle\ProductRetriever;


use ApaiIO\ApaiIO;
use ApaiIO\Operations\Lookup;

class AmazonRetriever implements  ProductRetriever{

    /**
     * @var ApaiIO
     */
    private $apaiIO;

    public function __construct(ApaiIO $apaiIO) {
        $this->apaiIO = $apaiIO;
    }

    public function retrieve($url) {
        //http://www.amazon.de/Erfolgreiche-Softwareprojekte-Web-Gedanken-Webentwicklung-ebook/dp/B00BIUFGRI/ref=sr_1_10%05ie=UTF8&qid=1416670722&sr=8-10&keywords=langner!

        $pattern = '^/dp/(.*)/^';

        echo preg_match($pattern, $url, $matches);

        //If the url did not match our requirements we dont want to do anything
        if (empty($matches[1])) {
            return null;
        }

        $lookup = new Lookup();

        $lookup->setItemId($matches[1]);
        $lookup->setResponseGroup(array('Large', 'Small'));
        $formattedResponse = $this->apaiIO->runOperation($lookup);

        //TODO DOM stuff
    }
} 