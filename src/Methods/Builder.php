<?php
namespace Chmeldax\PhpMocks\Methods;

use Chmeldax\PhpMocks\Exceptions\InvalidDefinitionException;

class Builder
{
    /** @var \ReflectionClass */
    private $reflection;
    
    /** @var object|null */
    private $instance;
    
    /**
     * @param \ReflectionClass $reflection
     * @param object $instance
     */
    public function __construct(\ReflectionClass $reflection, $instance = null)
    {
        $this->reflection = $reflection;
        $this->instance = $instance;
    }
    
    /**
     * @param string $methodName
     * @return \Chmeldax\PhpMocksMethods\Method
     */
    public function build($methodName)
    {
        $reflectionMethod = $this->reflection->getMethod($methodName);
        $this->checkIsPublic($reflectionMethod);
        $this->checkIsNotConstructor($reflectionMethod);
        return new Method($reflectionMethod, $this->instance);
    }
    
    /**
     * @param string $methodName
     * @return \Chmeldax\PhpMocksMethods\AllowedMethod
     */
    public function buildAllowed($methodName)
    {
        $reflectionMethod = $this->reflection->getMethod($methodName);
        $this->checkIsPublic($reflectionMethod);
        $this->checkIsNotConstructor($reflectionMethod);
        return new AllowedMethod($reflectionMethod, $this->instance);
    }
    
    private function checkIsPublic(\ReflectionMethod $reflectionMethod)
    {
        if(!$reflectionMethod->isPublic()) {
            $message = 'Method %s is no public! Non-public methods are not ' . 
                        'supported.';
            throw new InvalidDefinitionException(
                sprintf(
                    $message,
                    $reflectionMethod->getName()
                )
            );
        }
    }
    
    private function checkIsNotConstructor(\ReflectionMethod $reflectionMethod)
    {
        if($reflectionMethod->isConstructor()) {
            $message = 'Mocking __construct() method is not allowed.';
            throw new InvalidDefinitionException(
                sprintf(
                    $message,
                    $reflectionMethod->getName()
                )
            );
        }
    }
}
