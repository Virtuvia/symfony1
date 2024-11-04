<?php

/**
 * Profile form base class.
 *
 * @method     Profile getObject() Returns the current form's model object
 * @property   Profile $object The current form's model object
 *
 * @package    symfony12
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProfileForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id'         => new sfWidgetFormInputHidden(),
            'user_id'    => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('User'), 'add_empty' => true]),
            'first_name' => new sfWidgetFormInputText(),
            'last_name'  => new sfWidgetFormInputText(),
        ]);

        $this->setValidators([
            'id'         => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'user_id'    => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('User'), 'required' => false]),
            'first_name' => new sfValidatorString(['max_length' => 255, 'required' => false]),
            'last_name'  => new sfValidatorString(['max_length' => 255, 'required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('profile[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Profile';
    }
}
