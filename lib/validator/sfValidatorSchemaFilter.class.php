<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorSchemaFilter executes non schema validator on a schema input value.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorSchemaFilter.class.php 21908 2009-09-11 12:06:21Z fabien $
 */
class sfValidatorSchemaFilter extends sfValidatorSchema
{
  /**
   * Constructor.
   *
   * @param string          $field      The field name
   * @param sfValidatorBase $validator  The validator
   * @param array           $options    An array of options
   * @param array           $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  public function __construct($field, sfValidatorBase $validator, $options = array(), $messages = array())
  {
    $this->addOption('field', $field);
    $this->addOption('validator', $validator);

    parent::__construct(null, $options, $messages);
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    if (null === $value)
    {
      $value = array();
    }

    if (!is_array($value))
    {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method');
    }

    $fieldValue = isset($value[$this->getOption('field')]) ? $value[$this->getOption('field')] : null;

    try
    {
      $value[$this->getOption('field')] = $this->getOption('validator')->clean($fieldValue);
    }
    catch (sfValidatorError $error)
    {
      throw new sfValidatorErrorSchema($this, array($this->getOption('field') => $error));
    }

    return $value;
  }
}
