<?php

namespace App\Services\Parser;

class SparParserService
{
    public const MARKET_NAME_PATTERN = 'SPAR MARKET';
    public const TAX_NUMBER_PATTERN = 'ADÓSZÁM';
    public const TOTAL_PATTERN = 'ÖSSZESEN';
    public const CREDIT_CARD_PATTERN = 'BANKKARTYA';
    public const ID_PATTERN = 'NYUGTASZAM';

    public $rawText = '';
    // Receipt parameters that has to be extracted from the text.
    private $receipt = [
        'id' => '',
        'taxNumber' => '',
        'address' => '',
        'name' => '',
        'date' => '',
        'total' => '',
        'items' => [],
    ];

    public function __construct($rawText)
    {
        $this->rawText = $rawText;
    }

    public function parse()
    {
        $this->initReceipt();
        $this->parseSparReceipt();
        return $this->receipt;
    }

    private function initReceipt() {
        $this->receipt = [
            'id' => '',
            'taxNumber' => '',
            'address' => '',
            'name' => '',
            'date' => '',
            'total' => '',
            'items' => [],
        ];
    }

    private function parseSparReceipt() {
        // Parse the receipt line by line (delimiter is end of line)
        // Extract the receipt parameters
        $lines = explode("\n", $this->rawText);
        // the first line is the name of the store
        $this->receipt['name'] = trim($lines[0]);
        // Everything before the line that starts with the MARKET_NAME_PATTERN is the address
        $address = '';
        $marketNameLine = $this->getLineIndexWithLowestLevenshteinDistance($lines, self::MARKET_NAME_PATTERN);
        for ($index = 1; $index < $marketNameLine; $index++) {
            $address .= $lines[$index] . ' ';
        }
        $this->receipt['address'] = trim($address);
        $taxNumberLine = $this->getLineIndexWithLowestLevenshteinDistance($lines, self::TAX_NUMBER_PATTERN);
        $this->receipt['taxNumber'] = trim(substr($lines[$taxNumberLine], strpos($lines[$taxNumberLine], ' ')+1));
        $firstLineAfterItems = $this->parseSparItemLines($lines, 7);
        // The total price could be extracted from the line that starts with 'BANKKARTYA' or 'ÖSSZESEN:'
        $totalLineIndex = $this->getLineIndexWithLowestLevenshteinDistance($lines, self::TOTAL_PATTERN, $firstLineAfterItems);
        $creditCardLineIndex = $this->getLineIndexWithLowestLevenshteinDistance($lines, self::CREDIT_CARD_PATTERN, $firstLineAfterItems);
        // Check Total line. If the text similarity is higher than 0.8, then it is the total line.
        $totalLineSimilarityPercent = 0;
        similar_text(self::TOTAL_PATTERN, substr($lines[$totalLineIndex], 0, 8), $totalLineSimilarityPercent);
        $creditCardLineSimilarityPercent = 0;
        similar_text(self::CREDIT_CARD_PATTERN, substr($lines[$creditCardLineIndex], 0, 10), $creditCardLineSimilarityPercent);
        // The total price is the last number in the line followed by ' Ft'
        if ($totalLineSimilarityPercent > 80) {
            $firstSpace = strpos($lines[$totalLineIndex], ' ');
            $lastSpace = strrpos($lines[$totalLineIndex], ' ');
            $this->receipt['total'] = substr($lines[$totalLineIndex], $firstSpace, $lastSpace-$firstSpace);
        } else if ($creditCardLineSimilarityPercent > 80) {
            $firstSpace = strpos($lines[$creditCardLineIndex], ' ');
            $lastSpace = strrpos($lines[$creditCardLineIndex], ' ');
            $this->receipt['total'] = substr($lines[$creditCardLineIndex], $firstSpace, $lastSpace-$firstSpace);
        }
        // The id could be extracted from the line that starts with 'NYUGTASZAM:'. The id is followed by the ': '.
        // The date could be extracted from line after the id line. The date format is 'YYYY.MM.DD, HH:MM'
        $idLineIndex = $this->getLineIndexWithLowestLevenshteinDistance($lines, self::ID_PATTERN, $firstLineAfterItems);
        $this->receipt['id'] = substr($lines[$idLineIndex], strpos($lines[$idLineIndex], ' ')+2);
        $this->receipt['date'] = trim($lines[$idLineIndex+1]);
    }

    // Returns the first line index that is not item.
    private function parseSparItemLines($lines, $from)
    {
        // The lines containing the items are following the next pattern:
        // a prefix (3 character) followed by a space. The next characters are the name of the item, followed by a space and the price.
        // Start with the 7.th line. If we found the first line that does not match the pattern, set the itemParsing flag to true.
        // The items are finished, when the itemParsing flag is true and the next line does not match the pattern.
        $itemParsing = false;
        $item = [];
        for ($i = $from; $i < count($lines); $i++) {
            if (preg_match('/^[A-Z0-9]{3} /', $lines[$i])) {
                $itemParsing = true;
                $lastSpaceIndex = strrpos($lines[$i], ' ');
                $item['name'] = substr($lines[$i], 4, $lastSpaceIndex-4);
                $item['price'] = substr($lines[$i], $lastSpaceIndex+1);
                $this->receipt['items'][] = $item;
            } else {
                if ($itemParsing) {
                    return $i;
                }
            }
        }
    }

    // Return the line index of the line that has the lowest levenshtein distance to the given pattern.
    // If the pattern is not found, return -1.
    private function getLineIndexWithLowestLevenshteinDistance($lines, $pattern, $startIndex = 0)
    {
        $minDistance = 999999;
        $minDistanceIndex = -1;
        for ($i = $startIndex; $i < count($lines); $i++) {
            // Check only the first $pattern characters of the line.
            $distance = levenshtein(substr($lines[$i], 0, strlen($pattern)), $pattern);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $minDistanceIndex = $i;
            }
        }
        return $minDistanceIndex;
    }
}
