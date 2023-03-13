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

    private $readLineIndex = 0;

    public function parse(string $rawText): ScannedBasket
    {
        $this->rawText = $rawText;
        $this->receipt = new ScannedBasket();
        $this->parseSparReceipt();
        return $this->receipt;
    }

    private function parseSparReceipt() {
        $this->explodeTextIntoLines("\n");
        // the first line is the name of the company.
        $this->setupCompanyName();
        // The next lines are the address of the company.
        $this->setupCompanyAddress();
        // The next line holds the market name.
        $this->setupMarketName();
        // The next lines before the tax number are the address of the market.
        $this->setupMarketAddress($this->getLineIndexWithLowestLevenshteinDistance(self::TAX_NUMBER_PATTERN, $this->readLineIndex));
        // The tax number is followed by the 'ADÓSZÁM' pattern.
        $this->setupTaxNumber();
        $this->parseSparItemLines();
        $this->setupTotalPrice();
        // The id could be extracted from the line that starts with 'NYUGTASZAM:'. The id is followed by the ': '.
        // The date could be extracted from line after the id line. The date format is 'YYYY.MM.DD, HH:MM'
        $idLineIndex = $this->getLineIndexWithLowestLevenshteinDistance(self::ID_PATTERN, $this->readLineIndex);
        $this->receipt->basketId = trim(substr($this->lines[$idLineIndex], strpos($this->lines[$idLineIndex], ' ')+1));
        $this->receipt->date = trim($this->lines[$idLineIndex+1]);
    }

    // Returns the first line index that is not item.
    private function parseSparItemLines()
    {
        // The lines containing the items are following the next pattern:
        // a prefix (3 character) followed by a space. The next characters are the name of the item, followed by a space and the price.
        // If we found the first line that does not match the pattern, set the itemParsing flag to true.
        // The items are finished, when the itemParsing flag is true and the next line does not match the pattern.
        $itemParsing = false;
        $item = [];
        $numberOfLines = count($this->lines);
        for ($i = $this->readLineIndex; $i < $numberOfLines; $i++) {
            $currentLine = $this->lines[$i];
            // increment the read line index.
            $this->readLineIndex++;
            if (!preg_match('/^[A-Z0-9]{1,3} /', $currentLine)) {
                if ($this->notRelevantLine($currentLine)) {
                    continue;
                }
                if ($itemParsing) {
                    return $i;
                }
                continue;
            }
            $itemParsing = true;
            $lastSpaceIndex = strrpos($currentLine, ' ');
            $priceCandidateRaw = trim(substr($currentLine, $lastSpaceIndex+1));
            // replace every not digit character with empty string at the beginning of the string.
            $priceCandidate = preg_replace('/[^0-9]/', '', $priceCandidateRaw);
            // replace every not digit character with '0' string at the end of the string.
            //$priceCandidate = preg_replace('/[^0-9]+$/', '0', $priceCandidate);
            $item['quantity'] = 1;
            $item['quantity_unit_id'] = 3;

            if (is_numeric($priceCandidate)) {
                $item['price'] = $priceCandidate;
                $item['name'] = trim(substr($currentLine, 4, $lastSpaceIndex-4));
                $item['unit_price'] = floatVal($item['price']) / $item['quantity'];
                $this->receipt->items[] = $item;
                continue;
            }

            $item['name'] = trim(substr($currentLine, 4));
            // find the next not empty line
            while (empty(trim($this->lines[$i + 1]))) {
                $i++;
                $this->readLineIndex++;
            }
            $currentLine = $this->lines[$i + 1];
            // handle the kg units.
            if (strpos(strtolower($currentLine), 'kg') !== false) {
                $rawFloatNumber = str_replace(',', '.', trim(substr($currentLine, 0, strpos($currentLine, ' '))));
                // remove the not numeric characters from the beginning of the string.
                $rawFloatNumber = preg_replace('/^[^0-9]+/', '', $rawFloatNumber);
                $item['quantity'] = floatVal($rawFloatNumber);
                // if the quantity is 0, then it has to be updated to 1
                // to prevent division by zero.
                if ($item['quantity'] == 0) {
                    $item['quantity'] = 1;
                }
                $item['quantity_unit_id'] = 1;
            }
            $item['price'] = trim(substr($currentLine, strrpos($currentLine, ' ')+1));
            $item['unit_price'] = floatVal($item['price']) / $item['quantity'];
            $this->receipt->items[] = $item;
            $i++;
        }
    }

    /*
     * The company name is in the first line of the receipt.
     * */
    private function setupCompanyName()
    {
        $this->receipt->companyName = trim($this->lines[$this->readLineIndex]);
        $this->readLineIndex++;
    }
    /*
     * The company address is witten in the next 2 lines.
     * */
    private function setupCompanyAddress()
    {
        // If the first line is empty, then skip it.
        if (trim($this->lines[$this->readLineIndex]) == '') {
            $this->readLineIndex++;
        }
        $this->receipt->companyAddress = trim($this->lines[$this->readLineIndex]);
        $this->readLineIndex++;
        $this->receipt->companyAddress .= ' ' . trim($this->lines[$this->readLineIndex]);
        $this->readLineIndex++;
    }

    /*
     * The market name is in one line.
     * */
    private function setupMarketName()
    {
        $this->receipt->marketName = trim($this->lines[$this->readLineIndex]);
        $this->readLineIndex++;
        // look forward to the next lines. if the current line is empty
        if ($this->receipt->marketName == '') {
            if (trim($this->lines[$this->readLineIndex]) != '' && trim($this->lines[$this->readLineIndex+1]) == '') {
                $this->receipt->marketName = trim($this->lines[$this->readLineIndex]);
                $this->readLineIndex++;
                // skip the empty line
                $this->readLineIndex++;
            }
        }
    }

    /*
     * The market address might be in one or more lines.
     * */
    private function setupMarketAddress($lineIndex)
    {
        for ($index = $this->readLineIndex; $index < $lineIndex; $index++) {
            $this->receipt->marketAddress .= $this->lines[$index] . ' ';
            $this->readLineIndex++;
        }
        $this->receipt->marketAddress = trim($this->receipt->marketAddress);
    }
    /*
     * The tax number is in one line. The actual number id after the 'Adószám: '.
     * */
    private function setupTaxNumber()
    {
        $taxNumberLine= $this->lines[$this->readLineIndex];
        $this->receipt->taxNumber = trim(substr($taxNumberLine, strpos($taxNumberLine, ' ')+1));
        $this->readLineIndex++;
    }

    /*
     * The total price could be extracted from the line starts with 'Osszesen', or
     * from the line starts with the credit card pattern.
     * */
    private function setupTotalPrice()
    {
        // The total price could be extracted from the line that starts with 'BANKKARTYA' or 'ÖSSZESEN:'
        $totalLineIndex = $this->getLineIndexWithLowestLevenshteinDistance(self::TOTAL_PATTERN, $this->readLineIndex);
        $creditCardLineIndex = $this->getLineIndexWithLowestLevenshteinDistance(self::CREDIT_CARD_PATTERN, $this->readLineIndex);
        // Check Total line. If the text similarity is higher than 0.8, then it is the total line.
        $totalPercent = 0;
        similar_text(self::TOTAL_PATTERN, substr($this->lines[$totalLineIndex], 0, 8), $totalPercent);
        $creditCardPercent = 0;
        similar_text(self::CREDIT_CARD_PATTERN, substr($this->lines[$creditCardLineIndex], 0, 10), $creditCardPercent);
        // The total price is the last number in the line followed by ' Ft'
        if ($totalPercent > 80) {
            $firstSpace = strpos($this->lines[$totalLineIndex], ' ');
            $lastSpace = strrpos($this->lines[$totalLineIndex], ' ');
            $this->receipt->total = substr($this->lines[$totalLineIndex], $firstSpace, $lastSpace-$firstSpace);
        } else if ($creditCardPercent > 80) {
            $firstSpace = strpos($this->lines[$creditCardLineIndex], ' ');
            $lastSpace = strrpos($this->lines[$creditCardLineIndex], ' ');
            $this->receipt->total = substr($this->lines[$creditCardLineIndex], $firstSpace, $lastSpace-$firstSpace);
        }
    }

    /*
     * Checks a line for the given patterns. If a pattern is found, then it returns true.
     * */
    private function notRelevantLine($lineRaw)
    {
        // if the line is empty, then continue.
        if (empty(trim($lineRaw))) {
            return true;
        }
        $patterns = ['AFA', 'ÁFA', 'RESZOSSZESEN', 'Megnevez'];
        foreach ($patterns as $pattern) {
            if (strpos($lineRaw, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }
}
