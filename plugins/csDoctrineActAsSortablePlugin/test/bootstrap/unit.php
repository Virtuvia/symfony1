<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require 'autoload.php';

function doctrine_refresh()
{
    $args = func_get_args();
    foreach ($args as $arg)
    {
        $arg->refresh();
    }
}
