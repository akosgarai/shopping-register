<?php

namespace App\Services\Parser;

use App\ScannedBasket;
use App\Services\AbstractParserService;

class SparParserService extends AbstractParserService
{
    public const MARKET_NAME_PATTERN = 'SPAR MARKET';
    public const TAX_NUMBER_PATTERN = 'ADÓSZÁM';
    public const TOTAL_PATTERN = 'ÖSSZESEN';
    public const CREDIT_CARD_PATTERN = 'BANKKARTYA';
    public const ID_PATTERN = 'NYUGTASZAM';

    public $rawText = '';
    // Receipt parameters that has to be extracted from the text.
    private ScannedBasket $receipt;

    public function parse(string $rawText): ScannedBasket
    {
        $this->rawText = $rawText;
        $this->receipt = new ScannedBasket();
        $this->parseSparReceipt();
        return $this->receipt;
    }

    private function parseSparReceipt() {
        // Parse the receipt line by line (delimiter is end of line)
        // Extract the receipt parameters
        $lines = explode("\n", $this->rawText);
        // the first line is the name of the store
        $this->receipt->companyName = trim($lines[0]);
        // Everything before the line that starts with the MARKET_NAME_PATTERN is the address
        $companyAddress = '';
        $marketNameLine = $this->getLineIndexWithLowestLevenshteinDistance($lines, self::MARKET_NAME_PATTERN);
        $this->receipt->marketName = trim($lines[$marketNameLine]);
        for ($index = 1; $index < $marketNameLine; $index++) {
            $companyAddress .= $lines[$index] . ' ';
        }
        $this->receipt->companyAddress = trim($companyAddress);
        $taxNumberLine = $this->getLineIndexWithLowestLevenshteinDistance($lines, self::TAX_NUMBER_PATTERN);
        $this->receipt->taxNumber = trim(substr($lines[$taxNumberLine], strpos($lines[$taxNumberLine], ' ')+1));
        // The market address are in the lines between the MARKET_NAME_PATTERN and the TAX_NUMBER_PATTERN
        $marketAddress = '';
        for ($index = $marketNameLine + 1; $index < $taxNumberLine; $index++) {
            $marketAddress .= $lines[$index] . ' ';
        }
        $this->receipt->marketAddress = trim($marketAddress);
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
            $this->receipt->total = substr($lines[$totalLineIndex], $firstSpace, $lastSpace-$firstSpace);
        } else if ($creditCardLineSimilarityPercent > 80) {
            $firstSpace = strpos($lines[$creditCardLineIndex], ' ');
            $lastSpace = strrpos($lines[$creditCardLineIndex], ' ');
            $this->receipt->total = substr($lines[$creditCardLineIndex], $firstSpace, $lastSpace-$firstSpace);
        }
        // The id could be extracted from the line that starts with 'NYUGTASZAM:'. The id is followed by the ': '.
        // The date could be extracted from line after the id line. The date format is 'YYYY.MM.DD, HH:MM'
        $idLineIndex = $this->getLineIndexWithLowestLevenshteinDistance($lines, self::ID_PATTERN, $firstLineAfterItems);
        $this->receipt->basketId = trim(substr($lines[$idLineIndex], strpos($lines[$idLineIndex], ' ')+1));
        $this->receipt->date = trim($lines[$idLineIndex+1]);
    }

    // Returns the first line index that is not item.
    private function parseSparItemLines($lines, $from)
    {
        // The lines containing the items are following the next pattern:
        // a prefix (3 character) followed by a space. The next characters are the name of the item, followed by a space and the price.
        // If we found the first line that does not match the pattern, set the itemParsing flag to true.
        // The items are finished, when the itemParsing flag is true and the next line does not match the pattern.
        $itemParsing = false;
        $item = [];
        $numberOfLines = count($lines);
        for ($i = $from; $i < $numberOfLines; $i++) {
            if (!preg_match('/^[A-Z0-9]{1,3} /', $lines[$i])) {
                if ($itemParsing) {
                    return $i;
                }
                continue;
            }
            $itemParsing = true;
            $lastSpaceIndex = strrpos($lines[$i], ' ');
            $priceCandidate = trim(substr($lines[$i], $lastSpaceIndex+1));
            // replace every not digit character with empty string
            $priceCandidate = preg_replace('/[^0-9]/', '', $priceCandidate);
            $item['quantity'] = 1;
            $item['quantity_unit_id'] = 3;

            if (is_numeric($priceCandidate)) {
                $item['price'] = $priceCandidate;
                $item['name'] = trim(substr($lines[$i], 4, $lastSpaceIndex-4));
                $item['unit_price'] = floatVal($item['price']) / $item['quantity'];
                $this->receipt->items[] = $item;
                continue;
            }

            $item['name'] = trim(substr($lines[$i], 4));
            // find the next not empty line
            while (empty(trim($lines[$i+1]))) {
                $i++;
            }
            // handle the kg units.
            if (strpos(strtolower($lines[$i+1]), 'kg') !== false) {
                $item['quantity'] = floatVal(str_replace(',', '.', trim(substr($lines[$i+1], 0, strpos($lines[$i+1], ' ')))));
                // if the quantity is 0, then it has to be updated to 1
                // to prevent division by zero.
                if ($item['quantity'] == 0) {
                    $item['quantity'] = 1;
                }
                $item['quantity_unit_id'] = 1;
            }
            $item['price'] = trim(substr($lines[$i+1], strrpos($lines[$i+1], ' ')+1));
            $item['unit_price'] = floatVal($item['price']) / $item['quantity'];
            $this->receipt->items[] = $item;
            $i++;
        }
    }

    // Return the line index of the line that has the lowest levenshtein distance to the given pattern.
    // If the pattern is not found, return -1.
    private function getLineIndexWithLowestLevenshteinDistance($lines, $pattern, $startIndex = 0)
    {
        $minDistance = 999999;
        $minDistanceIndex = -1;
        $numberOfLines = count($lines);
        for ($i = $startIndex; $i < $numberOfLines ; $i++) {
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
