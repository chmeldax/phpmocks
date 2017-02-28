<?php
namespace PhpMocks\Constraints;

use PhpMocks\Exceptions\InvalidConstraintException;

class Anything implements Constraint
{   
    /**
     * 
     * @param mixed $actualValue
     * @return boolean
     */
    public function checkValue($actualValue)
    {
        return true;
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
        if(!is_null($class)) {
            $message = 'Using this constraint is not allowed since the ' .
                       'definition for parameter %s contains type hint.';
            throw new InvalidConstraintException(
                sprintf($message, $parameterReflection->getName())
            );
        }
        return true;
    }
}
