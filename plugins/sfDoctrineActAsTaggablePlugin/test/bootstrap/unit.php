<?php

declare(strict_types=1);

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__DIR__, 4) . '/config/autoload.php';

$manager = Doctrine_Manager::getInstance();
$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
$connection = $manager->openConnection($_SERVER['SF_DOCTRINE_ACT_AS_TAGGABLE_TEST_DSN'] ?? 'mysql://root@localhost/sfDoctrineActAsTaggable');
try {
    $connection->dropDatabase();
} catch (Doctrine_Connection_Mysql_Exception) {
}
$connection->createDatabase();
