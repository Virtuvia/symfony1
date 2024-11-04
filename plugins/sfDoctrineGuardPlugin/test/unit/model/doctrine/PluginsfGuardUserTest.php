<?php

/**
 * PluginsfGuardUser tests.
 */

require_once dirname(__DIR__, 3) . '/bootstrap/unit.php';

$unique = random_int(10000,99999);

$t = new lime_test(14);

$activeUser = new sfGuardUser();
$activeUser->first_name = 'John';
$activeUser->last_name = 'Doe';
$activeUser->email_address = 'email' . $unique .'@test.com';
$activeUser->username = 'active_user' . $unique;
$activeUser->password = 'password';
$activeUser->is_active = true;
$activeUser->save();

// ->__toString()
$t->diag('output functions');

$t->ok(strlen((string) $activeUser), '->__toString() returns a string');
$t->ok(strlen($activeUser->getName()), '->getName() returns a string');


// group managment
$t->diag('group managment');

$t->is($activeUser->getGroupNames(), array(), '->getGroupNames() return empty array if no group is set');

try
{
    $activeUser->addGroupByName('test-group' . $unique);
    $t->fail('->addGroupByName() does throw an exception if group not exist');
}
catch (Exception $e)
{
    $t->pass('->addGroupByName() does throw an exception if group not exist');
}

$group = new sfGuardGroup();
$group->name = 'test-group' . $unique;
$group->save();

$t->is($activeUser->hasGroup('test-group' . $unique), false, '->hasGroup() return false if user hasn\'t this group');

try
{
    $activeUser->addGroupByName('test-group' . $unique);
    $t->pass('->addGroupByName() does not throw an exception if group exist');
}
catch (Exception $e)
{
    $t->diag($e->getMessage());
    $t->fail('->addGroupByName() does not throw an exception if group exist');
}

$t->is($activeUser->getGroupNames(), array('test-group' . $unique), '->getGroupNames() return array with group names');
$t->is($activeUser->hasGroup('test-group' . $unique), true, '->hasGroup() return true if user has this group');


// permission managment
$t->diag('permission managment');

$t->is($activeUser->getPermissionNames(), array(), '->getPermissionNames() return empty array if no permission is set');

try
{
    $activeUser->addPermissionByName('test-permission' . $unique);
    $t->fail('->addPermissionByName() does throw an exception if group not exist');
}
catch (Exception $e)
{
    $t->pass('->addPermissionByName() does throw an exception if group not exist');
}

$permission = new sfGuardPermission();
$permission->name = 'test-permission' . $unique;
$permission->save();

$t->is($activeUser->hasPermission('test-permission' . $unique), false, '->hasPermission() return false if user hasn\'t this group');

try
{
    $activeUser->addPermissionByName('test-permission' . $unique);
    $t->pass('->addPermissionByName() does not throw an exception if permission exist');
}
catch (Exception $e)
{
    $t->diag($e->getMessage());
    $t->fail('->addPermissionByName() does not throw an exception if permission exist');
}

$t->is($activeUser->getPermissionNames(), array('test-permission' . $unique), '->getPermissionNames() return array with permission names');
$t->is($activeUser->hasPermission('test-permission' . $unique), true, '->hasPermission() return true if user has this group');
