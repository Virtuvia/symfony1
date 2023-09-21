<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

include dirname(__FILE__).'/../../bootstrap/unit.php';
require_once sfConfig::get('sf_symfony_lib_dir').'/vendor/lime/lime.php';

class TestTask extends sfBaseTask
{
  protected function execute($arguments = array(), $options = array())
  {
  }
}

$rootDir = dirname(__FILE__).'/../../functional/fixtures';
sfToolkit::clearDirectory($rootDir.'/cache');

$dispatcher = new sfEventDispatcher();
require_once $rootDir.'/config/ProjectConfiguration.class.php';
$configuration = new ProjectConfiguration($rootDir, $dispatcher);

$t = new lime_test(2);
$task = new TestTask($dispatcher, new sfFormatter());

// ->run()
$t->diag('->run()');

class ApplicationTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOption('application', null, sfCommandOption::PARAMETER_REQUIRED, '', true);
  }

  protected function execute($arguments = array(), $options = array())
  {
    if (!$this->configuration instanceof sfApplicationConfiguration)
    {
      throw new Exception('This task requires an application configuration be loaded.');
    }
  }
}

chdir($rootDir);

$task = new ApplicationTask($dispatcher, new sfFormatter());
try
{
  $task->run();
  $t->pass('->run() creates an application configuration if none is set');
}
catch (Exception $e)
{
  $t->diag($e->getMessage());
  $t->fail('->run() creates an application configuration if none is set');
}

$task = new ApplicationTask($dispatcher, new sfFormatter());
$task->setConfiguration($configuration);
try
{
  $task->run();
  $t->pass('->run() creates an application configuration if only a project configuration is set');
}
catch (Exception $e)
{
  $t->diag($e->getMessage());
  $t->fail('->run() creates an application configuration if only a project configuration is set');
}
