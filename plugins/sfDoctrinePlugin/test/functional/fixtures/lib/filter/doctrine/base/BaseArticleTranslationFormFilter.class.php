<?php

/**
 * ArticleTranslation filter form base class.
 *
 * @package    symfony12
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseArticleTranslationFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'title'       => new sfWidgetFormFilterInput(),
            'body'        => new sfWidgetFormFilterInput(),
            'test_column' => new sfWidgetFormFilterInput(),
            'slug'        => new sfWidgetFormFilterInput(),
        ]);

        $this->setValidators([
            'title'       => new sfValidatorPass(['required' => false]),
            'body'        => new sfValidatorPass(['required' => false]),
            'test_column' => new sfValidatorPass(['required' => false]),
            'slug'        => new sfValidatorPass(['required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('article_translation_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'ArticleTranslation';
    }

    public function getFields()
    {
        return [
            'id'          => 'Number',
            'title'       => 'Text',
            'body'        => 'Text',
            'test_column' => 'Text',
            'lang'        => 'Text',
            'slug'        => 'Text',
        ];
    }
}
