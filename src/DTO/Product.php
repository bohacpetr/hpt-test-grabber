<?php

declare(strict_types=1);

namespace HPT\DTO;

class Product
{

    /** @var string */
    private $id;
    /** @var float|null */
    private $price;
    /** @var string|null */
    private $name;
    /** @var int|null */
    private $ranking;

    public function __construct(string $id, ?float $price, ?string $name, ?int $ranking)
    {
        $this->id = $id;
        $this->price = $price;
        $this->name = $name;
        $this->ranking = $ranking;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRanking(): ?int
    {
        return $this->ranking;
    }
}
