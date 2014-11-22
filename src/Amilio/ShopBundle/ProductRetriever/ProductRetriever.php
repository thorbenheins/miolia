<?php
/**
 * Created by PhpStorm.
 * User: thorben
 * Date: 22/11/14
 * Time: 14:50
 */

namespace Amilio\ShopBundle\ProductRetriever;

use Amilio\CoreBundle\Entity\Product;


interface ProductRetriever {

    /**
     * @param string $url The url of the product that is
     *
     * @return Product
     */
    function retrieve(string $url);
} 