<?php

/**
 * User filter form base class.
 *
 * @package    symfony12
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseUserFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'username'         => new sfWidgetFormFilterInput(),
            'password'         => new sfWidgetFormFilterInput(),
            'test'             => new sfWidgetFormFilterInput(),
            'groups_list'      => new sfWidgetFormDoctrineChoice(['multiple' => true, 'model' => 'Group']),
            'permissions_list' => new sfWidgetFormDoctrineChoice(['multiple' => true, 'model' => 'Permission']),
        ]);

        $this->setValidators([
            'username'         => new sfValidatorPass(['required' => false]),
            'password'         => new sfValidatorPass(['required' => false]),
            'test'             => new sfValidatorPass(['required' => false]),
            'groups_list'      => new sfValidatorDoctrineChoice(['multiple' => true, 'model' => 'Group', 'required' => false]),
            'permissions_list' => new sfValidatorDoctrineChoice(['multiple' => true, 'model' => 'Permission', 'required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('user_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function addGroupsListColumnQuery(Doctrine_Query $query, $field, $values)
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        if (!count($values)) {
            return;
        }

        $query
            ->leftJoin($query->getRootAlias() . '.UserGroup UserGroup')
            ->andWhereIn('UserGroup.group_id', $values)
        ;
    }

    public function addPermissionsListColumnQuery(Doctrine_Query $query, $field, $values)
    {
        if (!is_array($values)) {
            $values = [$values];
        }

        if (!count($values)) {
            return;
        }

        $query
            ->leftJoin($query->getRootAlias() . '.UserPermission UserPermission')
            ->andWhereIn('UserPermission.permission_id', $values)
        ;
    }

    public function getModelName()
    {
        return 'User';
    }

    public function getFields()
    {
        return [
            'id'               => 'Number',
            'username'         => 'Text',
            'password'         => 'Text',
            'test'             => 'Text',
            'groups_list'      => 'ManyKey',
            'permissions_list' => 'ManyKey',
        ];
    }
}
