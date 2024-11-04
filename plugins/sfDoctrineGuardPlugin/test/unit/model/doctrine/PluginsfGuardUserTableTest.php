<?php

/**
 * PluginsfGuardUserTable tests.
 */

require_once dirname(__DIR__, 3) . '/bootstrap/unit.php';

$t = new lime_test(8);

$table = Doctrine_Core::getTable('sfGuardUser');
$users = $table->createQuery()
  ->whereIn('username', array('inactive_user', 'active_user'))
  ->execute();
$users->delete();

$inactiveUser = new sfGuardUser();
$inactiveUser->email_address = 'email@test.com';
$inactiveUser->username = 'inactive_user';
$inactiveUser->password = 'password';
$inactiveUser->is_active = false;
$inactiveUser->save();

$activeUser = new sfGuardUser();
$activeUser->email_address = 'email2@test.com';
$activeUser->username = 'active_user';
$activeUser->password = 'password';
$activeUser->is_active = true;
$activeUser->save();

// ->retrieveByUsername()
$t->diag('->retrieveByUsername()');

$t->is($table->retrieveByUsername('invalid'), null, '->retrieveByUsername() returns "null" if username is invalid');
$t->is($table->retrieveByUsername('inactive_user'), null, '->retrieveByUsername() returns "null" if user is inactive');
$t->isa_ok($table->retrieveByUsername('inactive_user', false), 'sfGuardUser', '->retrieveByUsername() returns an inactive user when second parameter is false');
$t->isa_ok($table->retrieveByUsername('active_user'), 'sfGuardUser', '->retrieveByUsername() returns an active user');
$t->is($table->retrieveByUsername('active_user', false), null, '->retrieveByUsername() returns "null" if user is active and second parameter is false');
$t->isa_ok($table->retrieveByUsername('active_user'), 'sfGuardUser', '->retrieveByUsername() can be called non-statically');

try
{
  $table->retrieveByUsername(null);
  $t->pass('->retrieveByUsername() does not throw an exception if username is null');
}
catch (Exception $e)
{
  $t->diag($e->getMessage());
  $t->fail('->retrieveByUsername() does not throw an exception if username is null');
}

$t->isa_ok(\Doctrine_Core::getTable(\sfGuardUser::class)->retrieveByUsername('active_user'), \sfGuardUser::class, '->retrieveByUsername() can be called statically (BC)');
