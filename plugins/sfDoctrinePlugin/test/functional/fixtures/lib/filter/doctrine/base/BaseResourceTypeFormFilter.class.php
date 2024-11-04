<?php

/**
 * ResourceType filter form base class.
 *
 * @package    symfony12
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseResourceTypeFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'name' => new sfWidgetFormFilterInput(),
        ]);

        $this->setValidators([
            'name' => new sfValidatorPass(['required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('resource_type_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'ResourceType';
    }

    public function getFields()
    {
        return [
            'id'   => 'Enum',
            'name' => 'Text',
        ];
    }
}
