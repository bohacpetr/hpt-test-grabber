<?php

declare(strict_types=1);

namespace HPT\Czc;

use DOMDocument;
use DOMNodeList;
use DOMNode;
use DOMXPath;
use HPT\DTO\Product;
use HPT\DTO\ProductList;
use HPT\Exceptions\InvalidProductCodeException;
use HPT\Exceptions\InvalidProductUrlException;
use HPT\Exceptions\MalformedResponseException;
use HPT\Exceptions\NotFoundException;
use HPT\Exceptions\ProductPriceNotFoundException;
use HPT\Grabber;
use Throwable;

class CzcGrabber implements Grabber
{

    private const CURL_OPTIONS = [
        CURLOPT_RETURNTRANSFER => true,
    ];

    private const TIDY_OPTIONS = [
        'output-xhtml' => true,
        'wrap' => false,
    ];

    /** @var ProductList */
    private $productList;
    /** @var string[] */
    private $config;

    public function __construct(array $config)
    {
        $this->productList = new ProductList();
        $this->config = $config;
    }

    public function getProductList(): ProductList
    {
        return $this->productList;
    }

    public function grabProduct(string $productId): void
    {
        $product = new Product($productId, null, null, null);

        try {
            $productLinkNodes = $this->searchProduct($productId);

            if ($productLinkNodes->length === 0) {
                throw new NotFoundException(sprintf('Product (%s) not found', $productId));
            }

            foreach ($productLinkNodes as $productLinkNode) {
                try {
                    /** @var $productLinkNode DOMNode */
                    $attribute = $productLinkNode->attributes->getNamedItem('href');

                    if ($attribute === null) {
                        throw new InvalidProductUrlException(sprintf('Invalid product (%s) URL', $productId));
                    }

                    $link = $productLinkNode->attributes->getNamedItem('href')->nodeValue;
                    $product = $this->fetchProductDetail(sprintf($this->config['detailUrlPattern'], $link), $productId);

                    break;
                } catch (InvalidProductCodeException|InvalidProductUrlException $e) {
                    // It's OK, continue to next product link
                }
            }

            if ($product->getPrice() === null) {
                throw new NotFoundException(sprintf('Product (%s) not found', $productId));
            }
        } catch (Throwable $e) {
            fwrite(STDERR, $e->getMessage() . PHP_EOL);
        }

        $this->productList->addProduct($product);
    }

    private function searchProduct(string $productId): DOMNodeList
    {
        $url = sprintf($this->config['searchUrlPattern'], $productId);
        $html = $this->fetchUrl($url);
        $dom = $this->loadDom($html);

        return $this->getNodeList($dom, $this->config['productDetailUrlXpath']);
    }

    private function fetchProductDetail(string $url, string $productId): Product
    {
        $html = $this->fetchUrl($url);
        $dom = $this->loadDom($html);
        $nodeList = $this->getNodeList($dom, $this->config['productCodeXpath']);

        if ($nodeList->length === 0 || $nodeList->item(0)->textContent !== $productId) {
            throw new InvalidProductCodeException(sprintf('Product code (%s) not found on detail page', $productId));
        }

        $nodeList = $this->getNodeList($dom, $this->config['productPriceXpath']);

        if ($nodeList->length === 0) {
            throw new ProductPriceNotFoundException(sprintf('Product (%s) price not found on detail page', $productId));
        }

        $priceText = $nodeList->item(0)->textContent;
        $price = str_replace([' Kč', ' ', ','], ['', '', '.'], $priceText);

        $nodeList = $this->getNodeList($dom, $this->config['productNameXpath']);

        $name = $nodeList->length !== 0
            ? $nodeList->item(0)->textContent
            : null;

        $nodeList = $this->getNodeList($dom, $this->config['productRankingXpath']);

        $ranking = $nodeList->length !== 0
            ? (int)$nodeList->item(0)->textContent
            : null;

        return new Product($productId, (float)$price, $name, $ranking);
    }

    private function fetchUrl(string $url): string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, self::CURL_OPTIONS);
        $html = curl_exec($ch);

        return tidy_repair_string($html, self::TIDY_OPTIONS, $this->config['encoding']) ?: '';
    }

    private function getNodeList(DOMDocument $dom, string $xpathQuery): DOMNodeList
    {
        $xpath = new DOMXPath($dom);

        return $xpath->query($xpathQuery);
    }

    private function loadDom(string $html): DOMDocument
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);

        if ($dom->loadHTML($html) === false) {
            throw new MalformedResponseException();
        }

        libxml_use_internal_errors(false);

        return $dom;
    }
}
