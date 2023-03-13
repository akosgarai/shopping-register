<?php

namespace App\Services;

use App\ScannedBasket;

abstract class AbstractParserService
{
    public $rawText = '';

    public $lines = [];

    // Receipt parameters that has to be extracted from the text.
    private ScannedBasket $receipt;

    abstract public function parse(string $rawText): ScannedBasket;

    // Return the line index of the line that has the lowest levenshtein distance to the given pattern.
    // If the pattern is not found, return -1.
    protected function getLineIndexWithLowestLevenshteinDistance($pattern, $startIndex = 0)
    {
        $minDistance = 999999;
        $minDistanceIndex = -1;
        $numberOfLines = count($this->lines);
        for ($i = $startIndex; $i < $numberOfLines ; $i++) {
            // Check only the first $pattern characters of the line.
            $distance = levenshtein(substr($this->lines[$i], 0, strlen($pattern)), $pattern);
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $minDistanceIndex = $i;
            }
        }
        return $minDistanceIndex;
    }

    // Explode the text into lines.
    protected function explodeTextIntoLines($delimiter = PHP_EOL)
    {
        $this->lines = explode($delimiter, $this->rawText);
    }
}
