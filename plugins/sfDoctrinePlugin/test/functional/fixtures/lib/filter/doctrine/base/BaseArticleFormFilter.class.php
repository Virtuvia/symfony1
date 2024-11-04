<?php

/**
 * Article filter form base class.
 *
 * @package    symfony12
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseArticleFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'author_id'      => new sfWidgetFormDoctrineChoice(['model' => $this->getRelatedModelName('Author'), 'add_empty' => true]),
            'is_on_homepage' => new sfWidgetFormChoice(['choices' => ['' => 'yes or no', 1 => 'yes', 0 => 'no']]),
            'title'          => new sfWidgetFormFilterInput(),
            'body'           => new sfWidgetFormFilterInput(),
            'test_column'    => new sfWidgetFormFilterInput(),
            'views'          => new sfWidgetFormFilterInput(),
            'type'           => new sfWidgetFormFilterInput(),
            'slug'           => new sfWidgetFormFilterInput(),
            'created_at'     => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false]),
            'updated_at'     => new sfWidgetFormFilterDate(['from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false]),
        ]);

        $this->setValidators([
            'author_id'      => new sfValidatorDoctrineChoice(['required' => false, 'model' => $this->getRelatedModelName('Author'), 'column' => 'id']),
            'is_on_homepage' => new sfValidatorChoice(['required' => false, 'choices' => ['', 1, 0]]),
            'title'          => new sfValidatorPass(['required' => false]),
            'body'           => new sfValidatorPass(['required' => false]),
            'test_column'    => new sfValidatorPass(['required' => false]),
            'views'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(['required' => false])),
            'type'           => new sfValidatorPass(['required' => false]),
            'slug'           => new sfValidatorPass(['required' => false]),
            'created_at'     => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
            'updated_at'     => new sfValidatorDateRange(['required' => false, 'from_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 00:00:00']), 'to_date' => new sfValidatorDateTime(['required' => false, 'datetime_output' => 'Y-m-d 23:59:59'])]),
        ]);

        $this->widgetSchema->setNameFormat('article_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'Article';
    }

    public function getFields()
    {
        return [
            'id'             => 'Number',
            'author_id'      => 'ForeignKey',
            'is_on_homepage' => 'Boolean',
            'title'          => 'Text',
            'body'           => 'Text',
            'test_column'    => 'Text',
            'views'          => 'Number',
            'type'           => 'Text',
            'slug'           => 'Text',
            'created_at'     => 'Date',
            'updated_at'     => 'Date',
        ];
    }
}
