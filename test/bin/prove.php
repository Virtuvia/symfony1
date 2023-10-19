#!/usr/bin/env php
<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$xml = $argv[1] ?? null;
$_SERVER['argv'][1] = $argv[1] = 'symfony:test';
if ($xml !== null) {
    $_SERVER['argv'][2] = $argv[2] = '--xml=' . (substr($xml, 0, 1) === '/' ? $xml : getcwd() . '/' . $xml);
}

require_once(dirname(__DIR__) . '/bootstrap/autoload.php');
chdir(dirname(__DIR__) . '/functional/fixtures');
include(dirname(__DIR__) . '/../lib/command/cli.php');
