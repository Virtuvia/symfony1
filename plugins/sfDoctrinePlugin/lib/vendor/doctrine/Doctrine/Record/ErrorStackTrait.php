<?php

declare(strict_types=1);

trait Doctrine_Record_ErrorStackTrait
{
    /**
     * @var null|Doctrine_Validator_ErrorStack   error stack object
     */
    private ?Doctrine_Validator_ErrorStack $_errorStack = null;

    /**
     * Get the record error stack as a human readable string.
     * Useful for outputting errors to user via web browser
     *
     * @todo rename to not get*
     *
     * @return string $message
     */
    public function getErrorStackAsString()
    {
        $errorStack = $this->getErrorStack();

        if (count($errorStack)) {
            $message = sprintf("Validation failed in class %s\n\n", get_class($this));

            $message .= "  " . count($errorStack) . " field" . (count($errorStack) > 1 ? 's' : null) . " had validation error" . (count($errorStack) > 1 ? 's' : null) . ":\n\n";
            foreach ($errorStack as $field => $errors) {
                $message .= "    * " . count($errors) . " validator" . (count($errors) > 1 ? 's' : null) . " failed on $field (" . implode(", ", $errors) . ")\n";
            }
            return $message;
        } else {
            return false;
        }
    }

    /**
     * retrieves the ErrorStack. To be called after a failed validation attempt (@see isValid()).
     *
     * @todo rename to not get*
     *
     * @return Doctrine_Validator_ErrorStack    returns the errorStack associated with this record
     */
    public function getErrorStack()
    {
        if (! $this->_errorStack) {
            $this->_errorStack = new Doctrine_Validator_ErrorStack(get_class($this));
        }

        return $this->_errorStack;
    }
}
