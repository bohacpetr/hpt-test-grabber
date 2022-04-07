<?php

declare(strict_types=1);

namespace HPT\Czc;

use HPT\DTO\ProductList;
use HPT\Output;

class CzcOutput implements Output
{

    public function getJson(ProductList $productList): string
    {
        $output = [];

        foreach ($productList as $product) {
            $output[$product->getId()] = $product->getPrice() !== null
                ? [
                    'price' => $product->getPrice(),
                    'name' => $product->getName(),
                    'ranking' => $product->getRanking(),
                ]
                : null;
        }

        return json_encode($output);
    }
}
