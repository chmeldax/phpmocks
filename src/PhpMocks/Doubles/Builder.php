<?php
namespace PhpMocks\Doubles;

class Builder
{
    /** @var object|null */
    private $instance;
    
    /** @var \ReflectionClass */
    private $reflection;
    
    /** @var PhpMocks\Methods\Method[] */
    private $methods = [];
    
    /** @var PhpMocks\Methods\Method[] */
    private $staticMethods = [];
    
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
        $this->appendMethod($allowedMethod, $methodName);
        return $allowedMethod;
    }
    
    /**
     * @return \PhpMocks\Doubles\Instance
     */
    public function build()
    {
        $generator = new Generator(
            $this->reflection,
            $this->methods,
            $this->staticMethods
        );
        return $generator->generate();
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
    
    private function appendMethod($method, $methodName)
    {
        if($method->isStatic()) {
            $this->staticMethods[$methodName] = $method;
        } else {
            $this->methods[$methodName] = $method;
        }
    }
}
