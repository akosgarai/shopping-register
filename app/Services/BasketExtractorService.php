<?php

namespace App\Services;

use Exception;

use App\ScannedBasket;
use App\Services\Parser\SparParserService;

class BasketExtractorService
{
    public const PARSER_SPAR = 'spar';
    // The text given by the OCR application.
    private string $rawText;

    private ScannedBasket $basket;

    public function parseTextWith(string $rawText, string $parser): ScannedBasket
    {
        $this->rawText = $rawText;
        $parserService = $this->getParserService($parser);

        return $this->extractBasketWith($parserService);
    }

    private function getParserService(string $parser): AbstractParserService
    {
        switch ($parser) {
            case self::PARSER_SPAR:
                return new SparParserService($this->rawText);
            default:
                throw new Exception('Parser not found');
        }
    }

    private function extractBasketWith(AbstractParserService $parserApplication): ScannedBasket
    {
        $this->basket = $parserApplication->parse($this->rawText);
        return $this->basket;
    }
}
