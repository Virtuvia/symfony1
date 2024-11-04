<?php

/**
 * SortableArticleCategory filter form base class.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSortableArticleCategoryFormFilter extends BaseFormFilterDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'name' => new sfWidgetFormFilterInput(),
        ]);

        $this->setValidators([
            'name' => new sfValidatorPass(['required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('sortable_article_category_filters[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'SortableArticleCategory';
    }

    public function getFields()
    {
        return [
            'id'   => 'Number',
            'name' => 'Text',
        ];
    }
}
