<?php

// The map for the parser application keys.
return [
    'spar' => [
        'label' => 'Spar',
        'parser' => \App\Services\Parser\SparParserService::class,
        'config' => [
            'lang' => 'hun',
            'user-pattern-file' => 'spar-patterns.txt',
            'user-words-file' => 'spar-words.txt',
            'psm' => 4,
            'oem' => 3,
        ],
    ],
];
