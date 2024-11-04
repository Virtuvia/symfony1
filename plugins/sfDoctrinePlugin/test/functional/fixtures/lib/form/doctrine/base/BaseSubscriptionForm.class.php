<?php

/**
 * Subscription form base class.
 *
 * @method     Subscription getObject() Returns the current form's model object
 * @property   Subscription $object The current form's model object
 *
 * @package    symfony12
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSubscriptionForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id'     => new sfWidgetFormInputHidden(),
            'name'   => new sfWidgetFormInputText(),
            'status' => new sfWidgetFormChoice(['choices' => array('New' => 'New', 'Active' => 'Active', 'Pending' => 'Pending', 'Expired' => 'Expired')]),
        ]);

        $this->setValidators([
            'id'     => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'name'   => new sfValidatorString(['max_length' => 255, 'required' => false]),
            'status' => new sfValidatorChoice(['choices' => array(0 => 'New', 1 => 'Active', 2 => 'Pending', 3 => 'Expired'), 'required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('subscription[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Subscription';
    }
}
