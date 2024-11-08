<?php

class myAutoload
{
    public static function autoload($class)
    {
        if ('myAutoloadedClass' == $class) {
            require_once(dirname(__FILE__) . '/myAutoloadedClass.class.php');

            return true;
        }

        return false;
    }
}
