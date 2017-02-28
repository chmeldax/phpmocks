<?php
namespace PhpMocks;

class Double
{   
    /** @var string */
    private $className;
    
    /** @var Method[] */
    private $methods = [];
    
    /** @var \ReflectionClass */
    private $reflection;
    
    /**
     * 
     * @param mixed $className
     */
    public function __construct($className)
    {
        $this->className = $className;
        $this->reflection = new \ReflectionClass($className);
    }
    
    public function allowMethodCall($methodName)
    {
        $methodReflection = $this->lookForMethod($methodName);
        $allowedMethod = new AllowedMethod($methodReflection);
        $this->methods[$methodName] = $allowedMethod;
        return $allowedMethod;
    }
    
    public function __call($name, $arguments)
    {
        if(!isset($this->methods[$name])) {
            throw new \Exception("Unexpected call to $name");
        }
        return $this->methods[$name]->performCall($arguments);
    }
    
    public static function __callStatic($name, $arguments) {
        throw new \InvalidArgumentException('Mocking of static methods is not supported.');
    }
    
    private function lookForMethod($methodName)
    {
        // IF is instance -> use ReflectionObject instead!
        $reflection = $this->reflectionClass->getMethod($methodName);
        if(!$reflection->isPublic() || $reflection->isStatic()) {
            throw new \Exception();
        }
        return $reflection;
    }
    
    private function loadReflection()
    {
        
    }
}
