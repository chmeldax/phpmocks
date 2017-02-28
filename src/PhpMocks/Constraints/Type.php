<?php
namespace PhpMocks\Constraints;

class Type implements Constraint
{
    private $typeName;
    
    private $type;
    
    const TYPE_PRIMITIVE = 1;
    const TYPE_CLASS = 2;
    const TYPE_INTERFACE = 4;
    const TYPE_TRAIT = 8;
    
    public function __construct($typeName)
    {
        $this->typeName = $typeName;
        $this->type = $this->getType($typeName);
    }
    
    public function checkValue($variable)
    {
        switch($this->typeName) {
            case 'string':
                return is_string($variable);
            case 'integer':
                return is_integer($variable);
            case 'array':
                return is_array($variable);
            case 'boolean':
                return is_bool($variable);
            default:
                return is_a($variable, $this->typeName);
        }
    }
    
    public function checkCompliance(\ReflectionParameter $parameterReflection)
    {
        if($this->type === self::TYPE_PRIMITIVE) {
            return true;
        }
        
        $typeName = $parameterReflection->getType();
        if(is_null($typeName)) {
            return true;
        }
        
        $reflectionClass = new \ReflectionClass($typeName);
        
        $type = $this->getType($typeName);
        
        if($this->type === self::TYPE_CLASS) {
            if($type !== $this->typeName && !in_array($type, class_implements($this->typeName)) && !in_array($type, class_parents($this->typeName))) {
                throw new \Exception;
            }
        }
        
        

        
        if(class_exists($this->typeName)) {

        } else if (interface_exists($this->typeName)) {
            if($type !== $this->typeName && !in_array($type, class_implements($this->typeName))) {
                throw new \Exception;
            }
        }
    }
    
    private function getType($typeName)
    {
        if(in_array($typeName, ['string', 'integer', 'array', 'boolean'])) {
            return self::TYPE_PRIMITIVE;
        }
        
        if(class_exists($typeName)) {
            return self::TYPE_CLASS;
        }
        
        if(interface_exists($typeName)) {
            return self::TYPE_INTERFACE;
        }
        
        if(trait_exists($typeName)) {
            return self::TYPE_TRAIT;
        }
        
        throw new \InvalidArgumentException('Unknown type');
    }
}

