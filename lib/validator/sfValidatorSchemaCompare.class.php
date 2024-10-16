<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorSchemaCompare compares several values from an array.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorSchemaCompare.class.php 21908 2009-09-11 12:06:21Z fabien $
 */
class sfValidatorSchemaCompare extends sfValidatorSchema
{
  const EQUAL              = '==';
  const NOT_EQUAL          = '!=';
  const IDENTICAL          = '===';
  const NOT_IDENTICAL      = '!==';
  const LESS_THAN          = '<';
  const LESS_THAN_EQUAL    = '<=';
  const GREATER_THAN       = '>';
  const GREATER_THAN_EQUAL = '>=';

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * left_field:         The left field name
   *  * operator:           The comparison operator
   *                          * self::EQUAL
   *                          * self::NOT_EQUAL
   *                          * self::IDENTICAL
   *                          * self::NOT_IDENTICAL
   *                          * self::LESS_THAN
   *                          * self::LESS_THAN_EQUAL
   *                          * self::GREATER_THAN
   *                          * self::GREATER_THAN_EQUAL
   *  * right_field:        The right field name
   *  * throw_global_error: Whether to throw a global error (false by default) or an error tied to the left field
   *
   * @param string $leftField   The left field name
   * @param string $operator    The operator to apply
   * @param string $rightField  The right field name
   * @param array  $options     An array of options
   * @param array  $messages    An array of error messages
   *
   * @see sfValidatorBase
   */
  public function __construct($leftField, $operator, $rightField, $options = array(), $messages = array())
  {
    $this->addOption('left_field', $leftField);
    $this->addOption('operator', $operator);
    $this->addOption('right_field', $rightField);

    $this->addOption('throw_global_error', false);

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

    $leftValue  = $value[$this->getOption('left_field')] ?? null;
    $rightValue = $value[$this->getOption('right_field')] ?? null;

    switch ($this->getOption('operator'))
    {
      case self::GREATER_THAN:
        $valid = $leftValue > $rightValue;
        break;
      case self::GREATER_THAN_EQUAL:
        $valid = $leftValue >= $rightValue;
        break;
      case self::LESS_THAN:
        $valid = $leftValue < $rightValue;
        break;
      case self::LESS_THAN_EQUAL:
        $valid = $leftValue <= $rightValue;
        break;
      case self::NOT_EQUAL:
        $valid = $leftValue != $rightValue;
        break;
      case self::EQUAL:
        $valid = $leftValue == $rightValue;
        break;
      case self::NOT_IDENTICAL:
        $valid = $leftValue !== $rightValue;
        break;
      case self::IDENTICAL:
        $valid = $leftValue === $rightValue;
        break;
      default:
        throw new InvalidArgumentException(sprintf('The operator "%s" does not exist.', $this->getOption('operator')));
    }

    if (!$valid)
    {
      $error = new sfValidatorError($this, 'invalid', array(
        'left_field'  => $leftValue,
        'right_field' => $rightValue,
        'operator'    => $this->getOption('operator'),
      ));
      if ($this->getOption('throw_global_error'))
      {
        throw $error;
      }

      throw new sfValidatorErrorSchema($this, array($this->getOption('left_field') => $error));
    }

    return $value;
  }
}
