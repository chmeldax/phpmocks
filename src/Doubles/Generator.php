<?php
namespace Chmeldax\PhpMocks\Doubles;

use gossi\codegen\generator\CodeGenerator;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;

class Generator
{
    /** @var \ReflectionClass */
    private $reflection;
    
    /** @var array */
    private $methods;
    
    /** @var array */
    private $staticMethods;
    
    /**
     *
     * @param \ReflectionClass $reflection
     * @param array $methods
     * @param array $staticMethods
     */
    public function __construct(\ReflectionClass $reflection, array $methods, array $staticMethods)
    {
        $this->reflection = $reflection;
        $this->methods = $methods;
        $this->staticMethods = $staticMethods;
    }
    
    public function generate()
    {
        $name = '\Chmeldax\\PhpMocks\\Doubles\\Instance' . uniqid();
        $class = PhpClass::fromFile(__DIR__ . '/Instance.php');
        $class->setQualifiedName($name);
        $this->setRelation($class);
        $this->generateMethods($class);
        $classDefinition = (new CodeGenerator)->generate($class);
        eval($classDefinition);
        return new $name($this->methods, $this->staticMethods);
    }
    
    private function generateMethods($class)
    {
        //Better handling of the arguments for abstract!
        $condition = \ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_ABSTRACT;
        foreach ($this->reflection->getMethods($condition) as $method) {
            if ($method->isConstructor()) {
                continue;
            }
            if ($method->isStatic()) {
                $class->setMethod($this->generateStaticMethod($method));
            } else {
                $class->setMethod($this->generateMethod($method));
            }
        }
    }
    
    private function generateStaticMethod($method)
    {
        $body = sprintf('return static::callStaticMethod("%s", func_get_args());', $method->getName());
        $methodDefinition = PhpMethod::create($method->getName())->setBody($body)->setStatic(true);
        foreach ($method->getParameters() as $parameter) {
            if ($parameter->isOptional()) {
                continue;
            }
            $methodDefinition->addParameter(PhpParameter::create()->setName($parameter->getName()));
        }
        return $methodDefinition;
    }
    
    private function generateMethod($method)
    {
        $body = sprintf('return $this->callMethod("%s", func_get_args());', $method->getName());
        $methodDefinition = PhpMethod::create($method->getName())->setBody($body);
        foreach ($method->getParameters() as $parameter) {
            if ($parameter->isOptional()) {
                continue;
            }
            $methodDefinition->addParameter($this->generateParameter($parameter));
        }
        return $methodDefinition;
    }
    
    private function setRelation($class)
    {
        $name = '\\' .$this->reflection->getName();
        if ($this->reflection->isInterface()) {
            $class->addInterface($name);
        } else {
            $class->setParentClassName('\\' . $this->reflection->getName());
        }
    }
    
    private function generateParameter($parameter)
    {
        $generatedParameter = PhpParameter::create();
        $generatedParameter->setName($parameter->getName());
        if (!is_null($parameter->getClass())) {
            $generatedParameter->setType('\\' . $parameter->getClass()->getName());
        }
        return $generatedParameter;
    }
}
