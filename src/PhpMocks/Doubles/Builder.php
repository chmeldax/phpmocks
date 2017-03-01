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
        if(array_key_exists($methodName, $this->methods)) {
            return $this->methods[$methodName];
        }
        if(array_key_exists($methodName, $this->staticMethods)) {
            return $this->staticMethods[$methodName];
        }
        $allowedMethod = $this->methodBuilder->buildAllowed($methodName);
        $this->appendMethod($allowedMethod, $methodName);
        return $allowedMethod;
    }
    
    /**
     * @param string $methodName
     * @return \PhpMocks\Doubles\Method
     */
    public function expectMethodCall($methodName)
    {
        if(array_key_exists($methodName, $this->methods)) {
            return $this->methods[$methodName];
        }
        if(array_key_exists($methodName, $this->staticMethods)) {
            return $this->staticMethods[$methodName];
        }
        $method = $this->methodBuilder->build($methodName);
        $this->appendMethod($method, $methodName);
        return $method;
    }
    
    /**
     * @return boolean
     */
    public function checkExpectations()
    {
        foreach($this->methods as $method) {
            $method->checkExpectations();
        }
        return true;
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
            $message = 'The argument $class should be an object or a string.';
            throw new \InvalidArgumentException($message);
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
