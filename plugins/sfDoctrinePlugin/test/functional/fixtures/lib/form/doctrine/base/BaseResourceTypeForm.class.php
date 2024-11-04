<?php

/**
 * ResourceType form base class.
 *
 * @method     ResourceType getObject() Returns the current form's model object
 * @property   ResourceType $object The current form's model object
 *
 * @package    symfony12
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseResourceTypeForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id'   => new sfWidgetFormInputHidden(),
            'name' => new sfWidgetFormInputText(),
        ]);

        $this->setValidators([
            'id'   => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'name' => new sfValidatorString(['max_length' => 255, 'required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('resource_type[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'ResourceType';
    }
}
