<?php
namespace PhpMocks\Constraints;

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
     * @throws \InvalidArgumentException
     */
    public function checkCompliance(\ReflectionParameter $parameterReflection)
    {
        $class = $parameterReflection->getClass();
        if(is_null($class)) {
            return true;
        }

        if(is_object($this->value) && !is_a($this->value, $class->name)) {
            throw new \InvalidArgumentException(
                sprintf("Supplied value does not comply with the method definition.", $this->value)
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

