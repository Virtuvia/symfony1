<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$xml = $argv[1] ?? '';
if (!empty($xml)) {
    if ((file_exists($xml) && !is_writable($xml)) || (!file_exists($xml) && !is_writable(dirname($xml)))) {
        throw new \RuntimeException(sprintf('unable xml to write to file, %s', $xml));
    }
}

require dirname(__DIR__) . '/test/bootstrap.php';

$h = new lime_harness();
$h->base_dir = dirname(__DIR__) . '/test';
$h->register(sfFinder::type('file')->name('*Test.php')->in([
    $h->base_dir
]));

$ret = $h->run() ? 0 : 1;

if (!empty($xml)) {
    file_put_contents($xml, $h->to_xml());

    exit(0);
}

exit($ret);
