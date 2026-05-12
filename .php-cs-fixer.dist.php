<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

return new Config()
    ->setCacheFile(__DIR__ . '/var/cache/php-cs-fixer')
    ->setFinder(
        Finder::create()
            ->in([
                __DIR__ . '/src',
                __DIR__ . '/tests',
            ])
            ->append([
                __FILE__,
            ]),
    )
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@PER-CS3.0' => true,
        '@PER-CS3.0:risky' => true,
    ])
;
