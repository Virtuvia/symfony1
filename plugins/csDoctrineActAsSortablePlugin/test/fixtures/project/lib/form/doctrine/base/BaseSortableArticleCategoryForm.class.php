<?php

/**
 * SortableArticleCategory form base class.
 *
 * @method     SortableArticleCategory getObject() Returns the current form's model object
 * @property   SortableArticleCategory $object The current form's model object
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSortableArticleCategoryForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id'   => new sfWidgetFormInputHidden(),
            'name' => new sfWidgetFormInputText(),
        ]);

        $this->setValidators([
            'id'   => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'name' => new sfValidatorString(['max_length' => 100, 'required' => false]),
        ]);

        $this->widgetSchema->setNameFormat('sortable_article_category[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'SortableArticleCategory';
    }
}
