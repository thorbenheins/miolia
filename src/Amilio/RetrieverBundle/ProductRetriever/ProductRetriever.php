<?php
/**
 * Created by PhpStorm.
 * User: thorben
 * Date: 22/11/14
 * Time: 14:50
 */

namespace Amilio\RetrieverBundle\ProductRetriever;

use Amilio\CoreBundle\Entity\Product;


interface ProductRetriever {

    public function canHandleUrl($url);
    
    /**
     * @param string $url The url of the product that is
     *
     * @return Product The Product object with its set properties. Or null if nothing was found.
     */
    function retrieve($url);
} 