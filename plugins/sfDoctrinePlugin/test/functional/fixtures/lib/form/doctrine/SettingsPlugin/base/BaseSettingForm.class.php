<?php

/**
 * Setting form base class.
 *
 * @method     Setting getObject() Returns the current form's model object
 * @property   Setting $object The current form's model object
 *
 * @package    symfony12
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSettingForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id'         => new sfWidgetFormInputHidden(),
            'name'       => new sfWidgetFormInputText(),
            'value'      => new sfWidgetFormTextarea(),
            'weight'     => new sfWidgetFormInputText(),
            'created_at' => new sfWidgetFormDateTime(),
        ]);

        $this->setValidators([
            'id'         => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'name'       => new sfValidatorString(['max_length' => 255]),
            'value'      => new sfValidatorString(['required' => false]),
            'weight'     => new sfValidatorNumber(['required' => false]),
            'created_at' => new sfValidatorDateTime(),
        ]);

        $this->widgetSchema->setNameFormat('setting[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Setting';
    }
}
