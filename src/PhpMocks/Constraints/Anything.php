<?php
namespace PhpMocks\Constraints;

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
     * @throws \InvalidArgumentException
     */
    public function checkCompliance(\ReflectionParameter $parameterReflection)
    {
        $class = $parameterReflection->getClass();
        if(!is_null($class)) {
            throw new \InvalidArgumentException('Anything is not acceptable');
        }
        return true;
    }
}

