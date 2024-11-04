<?php

/**
 * Attachment filter form base class.
 *
 * @package    symfony12
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAttachmentFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'file_path' => new sfWidgetFormFilterInput(),
        ]);

        $this->setValidators([
            'file_path' => new sfValidatorPass(['required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('attachment_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Attachment';
    }

    public function getFields()
    {
        return [
            'id'        => 'Number',
            'file_path' => 'Text',
        ];
    }
}
