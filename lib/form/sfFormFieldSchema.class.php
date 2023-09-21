<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFormFieldSchema represents an array of widgets bind to names and values.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFormFieldSchema.class.php 23214 2009-10-20 18:11:23Z Kris.Wallsmith $
 */
class sfFormFieldSchema extends sfFormField implements ArrayAccess, Iterator, Countable
{
  protected
    $count      = 0,
    $fieldNames = array(),
    $fields     = array();

  /**
   * Constructor.
   *
   * @param sfWidgetFormSchema $widget A sfWidget instance
   * @param sfFormField        $parent The sfFormField parent instance (null for the root widget)
   * @param string             $name   The field name
   * @param string             $value  The field value
   * @param sfValidatorError   $error  A sfValidatorError instance
   */
  public function __construct(sfWidgetFormSchema $widget, sfFormField $parent = null, $name, $value, sfValidatorError $error = null)
  {
    parent::__construct($widget, $parent, $name, $value, $error);

    $this->fieldNames = $widget->getPositions();
  }

  /**
   * Renders hidden form fields.
   *
   * @param boolean $recursive False will prevent hidden fields from embedded forms from rendering
   *
   * @return string
   */
  public function renderHiddenFields($recursive = true)
  {
    $output = '';

    foreach ($this->getHiddenFields($recursive) as $field)
    {
      $output .= $field->render();
    }

    return $output;
  }

  /**
   * Returns an array of hidden fields from the current schema.
   *
   * @param boolean $recursive Whether to recur through embedded schemas
   *
   * @return array
   */
  public function getHiddenFields($recursive = true)
  {
    $fields = array();

    foreach ($this as $name => $field)
    {
      if ($field instanceof sfFormFieldSchema && $recursive)
      {
        $fields = array_merge($fields, $field->getHiddenFields($recursive));
      }
      else if ($field->isHidden())
      {
        $fields[] = $field;
      }
    }

    return $fields;
  }

  /**
   * Returns true if the bound field exists (implements the ArrayAccess interface).
   *
   * @param mixed $offset The name of the bound field
   *
   * @return bool true if the widget exists, false otherwise
   */
  public function offsetExists($offset): bool
  {
    return isset($this->widget[$offset]);
  }

  /**
   * Returns the form field associated with the name (implements the ArrayAccess interface).
   *
   * @param mixed $offset The offset of the value to get
   *
   * @return sfFormField A form field instance
   */
  #[\ReturnTypeWillChange]
  public function offsetGet($offset)
  {
    if (!isset($this->fields[$offset]))
    {
      if (null === $widget = $this->widget[$offset])
      {
        throw new InvalidArgumentException(sprintf('Widget "%s" does not exist.', $offset));
      }

      $error = $this->error[$offset] ?? null;

      if ($widget instanceof sfWidgetFormSchema)
      {
        $class = 'sfFormFieldSchema';

        if ($error && !$error instanceof sfValidatorErrorSchema)
        {
          $error = new sfValidatorErrorSchema($error->getValidator(), array($error));
        }
      }
      else
      {
        $class = 'sfFormField';
      }

      $this->fields[$offset] = new $class($widget, $this, $offset, $this->value[$offset] ?? null, $error);
    }

    return $this->fields[$offset];
  }

  /**
   * Throws an exception saying that values cannot be set (implements the ArrayAccess interface).
   *
   * @param mixed $offset (ignored)
   * @param mixed $value (ignored)
   *
   * @throws LogicException
   */
  public function offsetSet($offset, $value): void
  {
    throw new LogicException('Cannot update form fields (read-only).');
  }

  /**
   * Throws an exception saying that values cannot be unset (implements the ArrayAccess interface).
   *
   * @param mixed $offset (ignored)
   *
   * @throws LogicException
   */
  public function offsetUnset($offset): void
  {
    throw new LogicException('Cannot remove form fields (read-only).');
  }

  /**
   * Resets the field names array to the beginning (implements the Iterator interface).
   */
  public function rewind(): void
  {
    reset($this->fieldNames);
    $this->count = count($this->fieldNames);
  }

  /**
   * Gets the key associated with the current form field (implements the Iterator interface).
   *
   * @return string The key
   */
  #[\ReturnTypeWillChange]
  public function key()
  {
    return current($this->fieldNames);
  }

  /**
   * Returns the current form field (implements the Iterator interface).
   *
   * @return mixed The escaped value
   */
  #[\ReturnTypeWillChange]
  public function current()
  {
    return $this[current($this->fieldNames)];
  }

  /**
   * Moves to the next form field (implements the Iterator interface).
   */
  public function next(): void
  {
    next($this->fieldNames);
    --$this->count;
  }

  /**
   * Returns true if the current form field is valid (implements the Iterator interface).
   *
   * @return boolean The validity of the current element; true if it is valid
   */
  public function valid(): bool
  {
    return $this->count > 0;
  }

  /**
   * Returns the number of form fields (implements the Countable interface).
   *
   * @return integer The number of embedded form fields
   */
  public function count(): int
  {
    return count($this->fieldNames);
  }
}
