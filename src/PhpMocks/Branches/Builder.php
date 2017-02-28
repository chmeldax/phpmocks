<?php
namespace PhpMocks\Branches;

use PhpMocks\Exceptions\InvalidDefinitionException;

class Builder
{
    /** @var \ReflectionMethod */ 
    private $methodReflection;
    
    /** @var \ReflectionParameter[]; */
    private $parameterReflections;
    
    /** @var object|null */
    private $instance;

    /**
     * 
     * @param \ReflectionMethod $methodReflection
     */
    public function __construct(\ReflectionMethod $methodReflection, $instance)
    {
        $this->methodReflection = $methodReflection;
        $this->parameterReflections = $this->methodReflection->getParameters();
        $this->instance = $instance;
    }
    
    /**
     * 
     * @param array $constraints
     */
    public function build(array $constraints)
    {
        $this->checkConstraints($constraints);
        $constraintObjects = [];
        $parameterNumber = 0;
        
        foreach($constraints as $constraint) {
            $constraintObject = $this->getConstraintObject($constraint);
            $parameterReflection = $this->getParameterReflection($parameterNumber);
            $constraintObject->checkCompliance($parameterReflection);
            $constraintObjects[] = $constraintObject;
            $parameterNumber++;
        }
        
        return new Branch($constraintObjects, $this->methodReflection, $this->instance);
    }
    
    private function checkConstraints($constraints)
    {
        $requiredParametersCount = $this->methodReflection->getNumberOfRequiredParameters();
        $parametersCount = $this->methodReflection->getNumberOfParameters();
        if(count($constraints) < $requiredParametersCount || (count($constraints) > $parametersCount && !$this->isVariadicPresent())) {
            $message = 'Number of parameters is incorrect.';
            throw new InvalidDefinitionException($message);
        }
    }
    
    private function isVariadicPresent()
    {
        if(count($this->parameterReflections) === 0) {
            return false;
        }
       return end($this->parameterReflections)->isVariadic(); 
    }
    
    private function getConstraintObject($constraint)
    {
        if(is_a($constraint, '\PhpMocks\Constraints\Constraint')) {
            return $constraint;
        }
        return new \PhpMocks\Constraints\Value($constraint);
    }
    
    private function getParameterReflection($parameterNumber)
    {
        if(array_key_exists($parameterNumber, $this->parameterReflections)) {
            return $this->parameterReflections[$parameterNumber];
        }
        
        if($this->isVariadicPresent()) {
            return end($this->parameterReflections);
        }
        
        throw new \RuntimeException;
    }
}
