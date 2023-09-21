<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/unit.php');

if (!isset($root_dir))
{
    $root_dir = realpath(dirname(__FILE__).sprintf('/../%s/fixtures', isset($type) ? $type : 'functional'));
}

require_once $root_dir.'/config/ProjectConfiguration.class.php';
$application = new sfSymfonyCommandApplication(new sfEventDispatcher(), new sfFormatter(), array(
  'symfony_lib_dir' => sfConfig::get('sf_symfony_lib_dir'),
));
