<?php

declare(strict_types=1);

namespace HPT;

use HPT\DTO\ProductList;

interface Output
{
    public function getJson(ProductList $productList): string;
}
