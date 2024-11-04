<?php

/**
 * DefaultValueTest filter form base class.
 *
 * @package    symfony12
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseDefaultValueTestFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'name' => new sfWidgetFormFilterInput(['with_empty' => false]),
        ]);

        $this->setValidators([
            'name' => new sfValidatorPass(['required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('default_value_test_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'DefaultValueTest';
    }

    public function getFields()
    {
        return [
            'id'   => 'Number',
            'name' => 'Text',
        ];
    }
}
