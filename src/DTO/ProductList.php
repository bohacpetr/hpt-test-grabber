<?php

declare(strict_types=1);

namespace HPT\DTO;

use IteratorAggregate;
use Traversable;

class ProductList implements IteratorAggregate
{

    /** @var Product[] */
    private $products = [];

    public function addProduct(Product $product)
    {
        $this->products[$product->getId()] = $product;
    }

    public function getIterator(): Traversable
    {
        foreach ($this->products as $product) {
            yield $product;
        }
    }
}
