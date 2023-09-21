<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
if (!include(dirname(__FILE__).'/../bootstrap/functional.php'))
{
  return;
}

$b = new sfTestBrowser();

$b->
  get('/autoload/myAutoload')->
  with('request')->begin()->
    isParameter('module', 'autoload')->
    isParameter('action', 'myAutoload')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('body div', 'foo')->
  end()
;

$t = $b->test();

$t->ok(class_exists('ExtendMe'), 'lib directory added to autoload');
$t->ok(class_exists('BaseExtendMe'), 'plugin lib directory added to autoload');
