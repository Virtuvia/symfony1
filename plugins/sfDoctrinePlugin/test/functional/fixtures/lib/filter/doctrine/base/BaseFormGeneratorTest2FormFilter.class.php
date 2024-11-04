<?php

/**
 * FormGeneratorTest2 filter form base class.
 *
 * @package    symfony12
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseFormGeneratorTest2FormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'name' => new sfWidgetFormFilterInput(),
        ]);

        $this->setValidators([
            'name' => new sfValidatorPass(['required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('form_generator_test2_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'FormGeneratorTest2';
    }

    public function getFields()
    {
        return [
            'id'   => 'Number',
            'name' => 'Text',
        ];
    }
}
