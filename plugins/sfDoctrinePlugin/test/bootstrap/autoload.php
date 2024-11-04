<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** @var Composer\Autoload\ClassLoader $classLoader */
$classLoader = require dirname(__DIR__, 4) . '/vendor/autoload.php';
$classLoader->addClassMap([
    'ProjectConfiguration' => dirname(__DIR__) . '/functional/fixtures/config/ProjectConfiguration.class.php',
]);

// autoload the classes from the test project
$fixturesProjectPath = dirname(__DIR__) . '/functional/fixtures';
$classPhpPathnames = array_map(
    static fn(\SplFileInfo $f): string => $f->getPathname(),
    array_filter(
        iterator_to_array(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fixturesProjectPath))),
        static fn(\SplFileInfo $f): bool => $f->isFile() && $f->getExtension() === 'php' && preg_match('/(.+)\.class\.php$/', $f->getFilename()),
    ),
);
$classes = [];
foreach ($classPhpPathnames as $classPhpPathname) {
    $classes[basename(preg_replace('/\.class\.php$/', '', $classPhpPathname))] = $classPhpPathname;
}
unset($classes['actions']);
ksort($classes);
$classLoader->addClassMap($classes);

define('SYMFONY_LIB_DIR', dirname(__DIR__, 4) . '/vendor/symfony/symfony1/lib');

require(SYMFONY_LIB_DIR . '/vendor/lime/lime.php');
