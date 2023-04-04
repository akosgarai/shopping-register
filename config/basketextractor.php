<?php

// The map for the parser application keys.
return [
    'spar' => [
        'label' => 'Spar',
        'parser' => \App\Services\Parser\SparParserService::class,
        'config' => [
            'lang' => 'hun',
        ],
    ],
];
