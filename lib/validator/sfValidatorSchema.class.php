<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Form\Util\ServerParams;

/**
 * sfValidatorSchema represents an array of fields.
 *
 * A field is a named validator.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorSchema.class.php 22446 2009-09-26 07:55:47Z fabien $
 */
class sfValidatorSchema extends sfValidatorBase implements ArrayAccess
{
  protected
    $fields        = array(),
    $preValidator  = null,
    $postValidator = null;

  /**
   * Constructor.
   *
   * The first argument can be:
   *
   *  * null
   *  * an array of named sfValidatorBase instances
   *
   * @param mixed $fields    Initial fields
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  public function __construct($fields = null, $options = array(), $messages = array())
  {
    if (is_array($fields))
    {
      foreach ($fields as $name => $validator)
      {
        $this[$name] = $validator;
      }
    }
    else if (null !== $fields)
    {
      throw new InvalidArgumentException('sfValidatorSchema constructor takes an array of sfValidatorBase objects.');
    }

    parent::__construct($options, $messages);
  }

  /**
   * Configures the validator.
   *
   * Available options:
   *
   *  * allow_extra_fields:  if false, the validator adds an error if extra fields are given in the input array of values (default to false)
   *  * filter_extra_fields: if true, the validator filters extra fields from the returned array of cleaned values (default to true)
   *
   * Available error codes:
   *
   *  * extra_fields
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addOption('allow_extra_fields', false);
    $this->addOption('filter_extra_fields', true);

    $this->addMessage('extra_fields', 'Unexpected extra form field named "%field%".');
    $this->addMessage('post_max_size', 'The form submission cannot be processed. It probably means that you have uploaded a file that is too big.');
  }

  /**
   * @see sfValidatorBase
   */
  public function clean($value)
  {
    return $this->doClean($value);
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

    $clean  = array();
    $unused = array_keys($this->fields);
    $errorSchema = new sfValidatorErrorSchema($this);

    $serverParams = new ServerParams();
    $contentLength = $serverParams->getContentLength();
    $maxContentLength = $serverParams->getPostMaxSize();

    // check that post_max_size has not been reached
    if (!empty($maxContentLength) && $contentLength > $maxContentLength)
    {
      $errorSchema->addError(new sfValidatorError($this, 'post_max_size'));

      throw $errorSchema;
    }

    // pre validator
    try
    {
        $value = $this->preClean($value);
    }
    catch (sfValidatorErrorSchema $e)
    {
      $errorSchema->addErrors($e);
    }
    catch (sfValidatorError $e)
    {
      $errorSchema->addError($e);
    }

    // validate given values
    foreach ($value as $name => $namedValue)
    {
      // field exists in our schema?
      if (!array_key_exists($name, $this->fields))
      {
        if (!$this->options['allow_extra_fields'])
        {
          $errorSchema->addError(new sfValidatorError($this, 'extra_fields', array('field' => $name)));
        }
        else if (!$this->options['filter_extra_fields'])
        {
          $clean[$name] = $namedValue;
        }

        continue;
      }

      unset($unused[array_search($name, $unused, true)]);

      // validate value
      try
      {
        $clean[$name] = $this->fields[$name]->clean($namedValue);
      }
      catch (sfValidatorError $e)
      {
        $clean[$name] = null;

        $errorSchema->addError($e, (string) $name);
      }
    }

    // are non given values required?
    foreach ($unused as $name)
    {
      // validate value
      try
      {
        $clean[$name] = $this->fields[$name]->clean(null);
      }
      catch (sfValidatorError $e)
      {
        $clean[$name] = null;

        $errorSchema->addError($e, (string) $name);
      }
    }

    // post validator
    try
    {
      $clean = $this->postClean($clean);
    }
    catch (sfValidatorErrorSchema $e)
    {
      $errorSchema->addErrors($e);
    }
    catch (sfValidatorError $e)
    {
      $errorSchema->addError($e);
    }

    if (count($errorSchema))
    {
      throw $errorSchema;
    }

    return $clean;
  }

  /**
   * Cleans the input values.
   *
   * This method is the first validator executed by doClean().
   *
   * It executes the validator returned by getPreValidator()
   * on the global array of values.
   *
   * @param  array $values  The input values
   *
   * @throws sfValidatorError
   */
  public function preClean($values)
  {
    if (null === $validator = $this->getPreValidator())
    {
      return $values;
    }

    return $validator->clean($values);
  }

  /**
   * Cleans the input values.
   *
   * This method is the last validator executed by doClean().
   *
   * It executes the validator returned by getPostValidator()
   * on the global array of cleaned values.
   *
   * @param  array $values  The input values
   *
   * @throws sfValidatorError
   */
  public function postClean($values)
  {
    if (null === $validator = $this->getPostValidator())
    {
      return $values;
    }

    return $validator->clean($values);
  }

  /**
   * Sets the pre validator.
   *
   * @param sfValidatorBase $validator  An sfValidatorBase instance
   *
   * @return sfValidatorBase The current validator instance
   */
  public function setPreValidator(sfValidatorBase $validator)
  {
    $this->preValidator = clone $validator;

    return $this;
  }

  /**
   * Returns the pre validator.
   *
   * @return sfValidatorBase A sfValidatorBase instance
   */
  public function getPreValidator()
  {
    return $this->preValidator;
  }

  /**
   * Sets the post validator.
   *
   * @param sfValidatorBase $validator  An sfValidatorBase instance
   *
   * @return sfValidatorBase The current validator instance
   */
  public function setPostValidator(sfValidatorBase $validator)
  {
    $this->postValidator = clone $validator;

    return $this;
  }

  /**
   * Returns the post validator.
   *
   * @return sfValidatorBase An sfValidatorBase instance
   */
  public function getPostValidator()
  {
    return $this->postValidator;
  }

  /**
   * Returns true if the schema has a field with the given name (implements the ArrayAccess interface).
   *
   * @param  mixed  $offset  The field name
   *
   * @return bool true if the schema has a field with the given name, false otherwise
   */
  public function offsetExists($offset): bool
  {
    return isset($this->fields[$offset]);
  }

  /**
   * Gets the field associated with the given name (implements the ArrayAccess interface).
   *
   * @param  mixed $offset  The field name
   *
   * @return sfValidatorBase The sfValidatorBase instance associated with the given name, null if it does not exist
   */
  #[\ReturnTypeWillChange]
  public function offsetGet($offset)
  {
    return $this->fields[$offset] ?? null;
  }

  /**
   * Sets a field (implements the ArrayAccess interface).
   *
   * @param mixed          $offset       The field name
   * @param sfValidatorBase $value  An sfValidatorBase instance
   */
  public function offsetSet($offset, $value): void
  {
    if (!$value instanceof sfValidatorBase)
    {
      throw new InvalidArgumentException('A field must be an instance of sfValidatorBase.');
    }

    $this->fields[$offset] = clone $value;
  }

  /**
   * Removes a field by name (implements the ArrayAccess interface).
   *
   * @param mixed $offset
   */
  public function offsetUnset($offset): void
  {
    unset($this->fields[$offset]);
  }

  /**
   * Returns an array of fields.
   *
   * @return sfValidatorBase An array of sfValidatorBase instances
   */
  public function getFields()
  {
    return $this->fields;
  }

  public function __clone()
  {
    foreach ($this->fields as $name => $field)
    {
      $this->fields[$name] = clone $field;
    }

    if (null !== $this->preValidator)
    {
      $this->preValidator = clone $this->preValidator;
    }

    if (null !== $this->postValidator)
    {
      $this->postValidator = clone $this->postValidator;
    }
  }
}
