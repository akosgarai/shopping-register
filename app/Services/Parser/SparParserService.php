<?php

namespace App\Services\Parser;

class SparParserService
{
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
        $marketNamePattern = 'SPAR MARKET';
        // the first line is the name of the store
        $this->receipt['name'] = $lines[0];
        // the next 2 line is the address
        $this->receipt['address'] = $lines[1].' '.$lines[2];
        // The tax number is the 7th line. The actual tax number starts after the ':'.
        $this->receipt['taxNumber'] = substr($lines[6], strpos($lines[6], ':')+1);
        $firstLineAfterItems = $this->parseSparItemLines($lines, 7);
        // The total price could be extracted from the line that starts with 'BANKKARTYA' or 'ÖSSZESEN:'
        // The total price is the last number in the line followed by ' Ft'
        // The id could be extracted from the line that starts with 'NYUGTASZAM:'. The id is followed by the ': '.
        // The date could be extracted from line after the id line. The date format is 'YYYY.MM.DD, HH:MM'
        $nextIsDate = false;
        for ($i = $firstLineAfterItems; $i < count($lines); $i++) {
            if ($nextIsDate) {
                $this->receipt['date'] = $lines[$i];
                $nextIsDate = false;
            }
            if (strpos($lines[$i], 'BANKKARTYA') !== false || strpos($lines[$i], 'ÖSSZESEN') !== false) {
                $firstSpace = strpos($lines[$i], ' ');
                $lastSpace = strrpos($lines[$i], ' ');
                $this->receipt['total'] = substr($lines[$i], $firstSpace, $lastSpace-$firstSpace);
            }
            if (strpos($lines[$i], 'NYUGTASZAM') !== false) {
                $this->receipt['id'] = substr($lines[$i], strpos($lines[$i], ' ')+2);
                $nextIsDate = true;
            }
        }
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
            if (preg_match('/^[A-Z]{3} /', $lines[$i])) {
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
}
