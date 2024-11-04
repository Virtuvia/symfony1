<?php

/**
 * BlogArticleTranslation form base class.
 *
 * @method     BlogArticleTranslation getObject() Returns the current form's model object
 * @property   BlogArticleTranslation $object The current form's model object
 *
 * @package    symfony12
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseBlogArticleTranslationForm extends BaseFormDoctrine
{
    public function setup()
    {
        $this->setWidgets([
            'id'          => new sfWidgetFormInputHidden(),
            'title'       => new sfWidgetFormInputText(),
            'body'        => new sfWidgetFormInputText(),
            'test_column' => new sfWidgetFormInputText(),
            'lang'        => new sfWidgetFormInputHidden(),
            'slug'        => new sfWidgetFormInputText(),
        ]);

        $this->setValidators([
            'id'          => new sfValidatorChoice(['choices' => [$this->getObject()->get('id')], 'empty_value' => $this->getObject()->get('id'), 'required' => false]),
            'title'       => new sfValidatorString(['max_length' => 255, 'required' => false]),
            'body'        => new sfValidatorString(['max_length' => 255, 'required' => false]),
            'test_column' => new sfValidatorString(['max_length' => 255, 'required' => false]),
            'lang'        => new sfValidatorChoice(['choices' => [$this->getObject()->get('lang')], 'empty_value' => $this->getObject()->get('lang'), 'required' => false]),
            'slug'        => new sfValidatorString(['max_length' => 255, 'required' => false]),
        ]);

        $this->validatorSchema->setPostValidator(
            new sfValidatorAnd([
                new sfValidatorDoctrineUnique(['model' => 'BlogArticleTranslation', 'column' => ['title']]),
                new sfValidatorDoctrineUnique(['model' => 'BlogArticleTranslation', 'column' => ['slug', 'lang', 'title']]),
            ]),
        );

        $this->widgetSchema->setNameFormat('blog_article_translation[%s]');

        $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

        $this->setupInheritance();

        parent::setup();
    }

    public function getModelName()
    {
        return 'BlogArticleTranslation';
    }
}
