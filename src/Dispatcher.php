<?php

declare(strict_types=1);

namespace HPT;

class Dispatcher
{

    /** @var Grabber */
    private $grabber;
    /** @var Output */
    private $output;
    /** @var string */
    private $sourceFile;

    public function __construct(Grabber $grabber, Output $output, string $sourceFile)
    {
        $this->grabber = $grabber;
        $this->output = $output;
        $this->sourceFile = $sourceFile;
    }

    /**
     * @return string JSON
     */
    public function run(): string
    {
        $f = fopen(BASE_DIR . $this->sourceFile, 'r');

        while (($productId = fgets($f, 1000)) !== false) {
            $productId = rtrim($productId, "\n\r");
            $this->grabber->getPrice($productId);
        }

        return $this->output->getJson();
    }
}
