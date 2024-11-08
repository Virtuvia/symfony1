<?php

declare(strict_types=1);

use Symfony\Component\Finder\Finder;

$finder = (new Finder())
    ->files()
    ->name('/\.php$/')
    ->in(__DIR__)
    ->exclude([
        'test/unit/config/fixtures/sfDefineEnvironmentConfigHandler',
        'test/unit/config/fixtures/sfFilterConfigHandler',
        'plugins/sfDoctrinePlugin/data/generator',
        'plugins/csDoctrineActAsSortablePlugin/test/fixtures/project/lib/model/doctrine',
        'plugins/sfDoctrinePlugin/test/functional/fixtures/lib/model/doctrine',
        'plugins/sfDoctrinePlugin/test/functional/fixtures/lib/model/doctrine',
        'plugins/sfDoctrinePlugin/test/functional/fixtures/lib/filter',
        'plugins/sfDoctrinePlugin/test/functional/fixtures/lib/form',
    ])
;

$config = new \PhpCsFixer\Config();
$config
    ->setRules([
        '@PER-CS2.0' => true,
        'method_argument_space' => ['on_multiline' => 'ignore'],
        'single_line_empty_body' => false,
        'phpdoc_scalar' => true,
        'phpdoc_types' => true,
    ])
    ->setFinder($finder)
;

return $config;
