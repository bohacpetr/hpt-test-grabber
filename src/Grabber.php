<?php

declare(strict_types=1);

namespace HPT;

use HPT\DTO\ProductList;

interface Grabber
{
    public function grabProduct(string $productId): void;
    public function getProductList(): ProductList;
}
