<?php

$app = 'frontend';
include dirname(__FILE__) . '/../../bootstrap/functional.php';

$t = new lime_test(2);

// ->__construct()
$t->diag('->__construct()');

$article = new Article();
try {
    $article->setAuthor(new stdClass());
} catch (Exception $e) {
    $t->is($e->getMessage(), 'Couldn\'t call Doctrine_Core::set(), second argument should be an instance of Doctrine_Record or Doctrine_Null when setting one-to-one references.', 'Making sure proper exception message is thrown');
}

try {
    $test = new ModelWithNumberInColumn();
    $test->getColumn_1();
    $test->getColumn_2();
    $test->getColumn__3();
    $t->pass('Make sure __call() handles fields with *_(n) in the field name');
} catch (Exception $e) {
    $t->fail('__call() failed in sfDoctrineRecord');
}
