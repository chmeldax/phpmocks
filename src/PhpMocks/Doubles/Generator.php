<?php
namespace PhpMocks\Doubles;

use gossi\codegen\generator\CodeGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;

class Generator
{
    /** @var \ReflectionClass */
    private $reflection;
    
    /** @var array */
    private $methods;
    
    /**
     * 
     * @param \ReflectionClass $reflection
     * @param array $methods
     */
    public function __construct(\ReflectionClass $reflection, array $methods)
    {
        $this->reflection = $reflection;
        $this->methods = $methods;
    }
    
    public function generate()
    {
        $name = '\PhpMocks\\Doubles\\Instance' . uniqid();
        $class = PhpClass::fromFile(__DIR__ . '/Instance.php');
        $class->setQualifiedName($name);
        $class->setParentClassName('\\' . $this->reflection->getName());
        $this->generateMethods($class);
        $classDefinition =  (new CodeGenerator)->generate($class);
        eval($classDefinition);
        return new $name($this->methods);
    }
    
    private function generateMethods($class)
    {
        //Better handling of the arguments for abstract!
        $condition = \ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_ABSTRACT;
        foreach($this->reflection->getMethods($condition) as $method) {
            $methodName = $method->getName();
            if($method->isStatic()) {
                $class->setMethod($this->generateStaticMethod($methodName));
            } else {
                $class->setMethod($this->generateMethod($methodName));
            }
        }
    }
    
    private function generateStaticMethod($methodName)
    {
        $body = 'return static::callStaticMethod();';
        return PhpMethod::create($methodName)->setBody($body)->setStatic(true);
    }
    
    private function generateMethod($methodName)
    {
        $body = sprintf('return $this->callMethod("%s", func_get_args());', $methodName);
        return PhpMethod::create($methodName)->setBody($body);
    }
}
