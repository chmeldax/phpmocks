<?php
namespace Chmeldax\PhpMocks\Constraints;

use Chmeldax\PhpMocks\Exceptions\InvalidConstraintException;

class Value implements Constraint
{
    /** @var mixed */
    private $value;
    
    /**
     * 
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    /**
     * 
     * @param \ReflectionParameter $parameterReflection
     * @return boolean
     * @throws InvalidConstraintException
     */
    public function checkCompliance(\ReflectionParameter $parameterReflection)
    {
        $class = $parameterReflection->getClass();
        if(is_null($class)) {
            return true;
        }

        if(is_object($this->value) && !is_a($this->value, $class->name)) {
            $message = 'Supplied value for parameter %s does not comply with ' .
                       'the method definition!';
            throw new InvalidConstraintException(
                sprintf($message, $parameterReflection->getName())
            );
        }
        return true;
    }
    
    /**
     * 
     * @param mixed $actualValue
     * @return bool
     */
    public function checkValue($actualValue)
    {
        return $actualValue === $this->value;
    }
}
