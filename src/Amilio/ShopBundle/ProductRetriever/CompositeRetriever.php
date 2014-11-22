<?php
namespace Amilio\ShopBundle\ProductRetriever;

class CompositeRetriever implements ProductRetriever
{

    private $retrievers = array();

    public function canHandleUrl($url)
    {
        foreach ($this->retrievers as $retriever) {
            if ($retriever->canHandleUrl($url)) {
                return true;
            }
        }
        return false;
    }

    public function addRetriever(ProductRetriever $retriever)
    {
        $this->retrievers[] = $retriever;
    }

    public function retrieve($url)
    {
        $product = null;
        
        foreach ($this->retrievers as $retriever) {
            if ($retriever->canHandleUrl($url)) {
                $product = $retriever->retrieve($url);
            }
        }
        
        return $product;
    }
}