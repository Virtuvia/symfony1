<?php

/**
 * CamelCase form base class.
 *
 * @method     CamelCase getObject() Returns the current form's model object
 * @property   CamelCase $object The current form's model object
 *
 * @package    symfony12
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCamelCaseForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id'            => new sfWidgetFormInputHidden(),
            'article_id'    => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Article'), 'add_empty' => true]),
            'testCamelCase' => new sfWidgetFormInputText(),
        ]);

        $this->setValidators([
            'id'            => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'article_id'    => new sfValidatorDoctrineChoice(['model' => $this->getRelatedModelName('Article'), 'required' => false]),
            'testCamelCase' => new sfValidatorString(['max_length' => 255, 'required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('camel_case[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'CamelCase';
    }
}
