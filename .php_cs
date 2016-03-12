<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__.'/app/migrations')
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests');

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        '-empty_return',
        '-phpdoc_separation',
        '-pre_increment',
        'ordered_use',
        'short_array_syntax',
    ])
    ->finder($finder);
