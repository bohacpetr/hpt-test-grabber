<?php

declare(strict_types=1);

namespace HPT\Czc;

use HPT\ProductList;
use HPT\Output;

class CzcOutput implements Output
{

    /** @var ProductList */
    private $productList;

    public function __construct(ProductList $productPrices)
    {
        $this->productList = $productPrices;
    }

    public function getJson(): string
    {
        $output = [];

        foreach ($this->productList as $product) {
            $output[$product->getId()] = $product->getPrice() !== null
                ? ['price' => $product->getPrice()]
                : null;
        }

        return json_encode($output);
    }
}
