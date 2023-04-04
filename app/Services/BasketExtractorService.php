<?php

namespace App\Services;

use Exception;

use App\ScannedBasket;

class BasketExtractorService
{
    // The text given by the OCR application.
    private string $rawText;

    private ScannedBasket $basket;

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function parseTextWith(string $rawText, string $parser): ScannedBasket
    {
        $this->rawText = $rawText;
        $parserService = $this->getParserService($parser);

        return $this->extractBasketWith($parserService);
    }

    public function getParserApplications(): array
    {
        // return the keys and labels of the parsers.
        $parsers = [];
        foreach ($this->config as $key => $value) {
            $parsers[] = ['name' => $key, 'label' => $value['label']];
        }
        return $parsers;
    }

    private function getParserService(string $parser): AbstractParserService
    {
        if (!array_key_exists($parser, $this->config)) {
            throw new Exception('Parser not found ' . $parser);
        }
        return new $this->config[$parser]['parser']($this->rawText);
    }

    private function extractBasketWith(AbstractParserService $parserApplication): ScannedBasket
    {
        $this->basket = $parserApplication->parse($this->rawText);
        return $this->basket;
    }
}
