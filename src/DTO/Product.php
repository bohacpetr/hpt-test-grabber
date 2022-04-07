<?php

declare(strict_types=1);

namespace HPT\DTO;

class Product
{

    /** @var string */
    private $id;
    /** @var float|null */
    private $price;

    public function __construct(string $id, ?float $price)
    {
        $this->id = $id;
        $this->price = $price;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }
}
