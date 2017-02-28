<?php
namespace PhpMocks\Doubles;

class Builder
{
    /** @var object|null */
    private $instance;
    
    /** @var \ReflectionClass */
    private $reflection;
    
    /** @var PhpMocks\Methods\Method[] */
    private $methods;
    
    /** @var \PhpMocks\Methods\Builder */
    private $methodBuilder;
    
    /**
     * @param string|object $class
     */
    public function __construct($class)
    {
        $this->processClass($class);
        $this->methodBuilder = new \PhpMocks\Methods\Builder(
            $this->reflection,
            $this->instance
        );
    }
    
    /**
     * @param string $methodName
     * @return \PhpMocks\Doubles\AllowedMethod
     */
    public function allowMethodCall($methodName)
    {
        $allowedMethod = $this->methodBuilder->build($methodName);
        $this->methods[$methodName] = $allowedMethod;
        return $allowedMethod;
    }
    
    /**
     * @return \PhpMocks\Doubles\Instance
     */
    public function build()
    {
        return new Instance($this->methods);
    }
    
    private function processClass($class)
    {
        if(is_string($class)) {
            $this->reflection = new \ReflectionClass($class);
        } else if (is_object($class)) {
            $this->reflection = new \ReflectionObject($class);
            $this->instance = $class;
        }  else {
            throw new \InvalidArgumentException('The argument $class should be an object or a string.');
        }
    }
}
