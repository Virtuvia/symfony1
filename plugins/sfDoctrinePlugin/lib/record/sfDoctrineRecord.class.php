<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base sfDoctrineRecord extends the base Doctrine_Record in Doctrine to provide some
 * symfony specific functionality to Doctrine_Records
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineRecord.class.php 29659 2010-05-28 16:49:48Z Kris.Wallsmith $
 */
abstract class sfDoctrineRecord extends Doctrine_Record
{
    /**
     * Returns the current record's primary key.
     *
     * This a proxy method to {@link Doctrine_Record::identifier()} for
     * compatibility with a Propel-style API.
     *
     * @return mixed The value of the current model's last identifier column
     */
    public function getPrimaryKey()
    {
        $identifier = (array) $this->identifier();
        return end($identifier);
    }

    /**
     * Function require by symfony >= 1.2 admin generators.
     *
     * @return bool
     */
    public function isNew()
    {
        return ! $this->exists();
    }

    /**
     * Returns a string representation of the record.
     *
     * @return string A string representation of the record
     */
    public function __toString(): string
    {
        $guesses = ['name',
            'title',
            'description',
            'subject',
            'keywords',
            'id'];

        // we try to guess a column which would give a good description of the object
        foreach ($guesses as $descriptionColumn) {
            try {
                return (string) $this->get($descriptionColumn);
            } catch (Exception $e) {
            }
        }

        return sprintf('No description for object of class "%s"', $this->getTable()->getComponentName());
    }

    /**
     * Provides getter and setter methods.
     *
     * @param  string $method    The method name
     * @param  array  $arguments The method arguments
     *
     * @return mixed The returned value of the called method
     */
    public function __call($method, $arguments)
    {
        $failed = false;
        try {
            if (in_array($verb = substr($method, 0, 3), ['set', 'get'])) {
                $name = substr($method, 3);

                $table = $this->getTable();
                if ($table->hasRelation($name)) {
                    $entityName = $name;
                } elseif ($table->hasField($fieldName = $table->getFieldName($name))) {
                    $entityNameLower = strtolower($fieldName);
                    if ($table->hasField($entityNameLower)) {
                        $entityName = $entityNameLower;
                    } else {
                        $entityName = $fieldName;
                    }
                } else {
                    $underScored = $table->getFieldName(sfInflector::underscore($name));
                    if ($table->hasField($underScored) || $table->hasRelation($underScored)) {
                        $entityName = $underScored;
                    } elseif ($table->hasField(strtolower($name)) || $table->hasRelation(strtolower($name))) {
                        $entityName = strtolower($name);
                    } else {
                        $camelCase = $table->getFieldName(sfInflector::camelize($name));
                        $camelCase = strtolower($camelCase[0]) . substr($camelCase, 1, strlen($camelCase));
                        if ($table->hasField($camelCase) || $table->hasRelation($camelCase)) {
                            $entityName = $camelCase;
                        } else {
                            $entityName = $underScored;
                        }
                    }
                }

                return call_user_func_array(
                    [$this, $verb],
                    array_merge([$entityName], $arguments),
                );
            } else {
                $failed = true;
            }
        } catch (\Throwable $e) {
            $failed = true;
        }
        if ($failed) {
            try {
                return parent::__call($method, $arguments);
            } catch (Doctrine_Record_UnknownPropertyException $e2) {
            }

            if (isset($e) && $e) {
                throw $e;
            } elseif (isset($e2) && $e2) {
                throw $e2;
            }
        }
    }
}
