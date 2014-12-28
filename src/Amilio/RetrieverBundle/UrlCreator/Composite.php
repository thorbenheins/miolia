<?php

namespace Amilio\RetrieverBundle\UrlCreator;

use Amilio\CoreBundle\Entity\Product;

use Amilio\CoreBundle\Entity\User;

// @todo refactor this! very soon
class Composite
{
    public function getUrl(Product $product, User $user = null, User $amilioUser = null)
    {
        $fid = $product->getForeignId();
        if ($fid != '') {
            if (strpos($fid, 'amazon') === 0) {
                return "http://www.amazon.de/gp/product/" . substr($fid, 7) . '?tag=amilio-21';
            }
        }
        return $product->getUrl();
    }
}
