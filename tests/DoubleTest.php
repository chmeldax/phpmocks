<?php
namespace Chmeldax\PhpMocks;

class DoubleTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanceWithTypeHints()
    {
        $stdClass = new \stdClass();
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('methodWithTypeHint')
            ->with(new Constraints\Anything, new Constraints\Anything, $stdClass)
            ->andReturn('return_value_1');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodWithTypeHint('str1', 'str2', $stdClass));
    }
    
    public function testInstanceConsecutiveCalls()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('methodConsecutive')
            ->with()
            ->andReturn('return_value_1', 'return_value_2');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodConsecutive());
        $this->assertEquals('return_value_2', $double->methodConsecutive());
    }
    
    public function testInstanceCallback()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('methodCallback')
            ->with('return_value_1')
            ->andInvoke(function ($whatever) {
                return $whatever;
            });
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodCallback('return_value_1'));
    }
    
    /**
     * @expectedException \Chmeldax\PhpMocks\TestFixtures\TestException
     */
    public function testInstanceException()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('methodException')
            ->with()
            ->andThrow(new TestFixtures\TestException);
        $double = $doubleBuilder->build();
        
        $double->methodException();
    }
    
    public function testInstanceCallOriginal()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('methodCallOriginal')
            ->with('return_value_1')
            ->andCallOriginal();
        $double = $doubleBuilder->build();
        
        $this->assertEquals('original return_value_1', $double->methodCallOriginal('return_value_1'));
    }
    
    public function testInstanceVariadic()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('methodVariadic')
            ->with(1, 2, 3)
            ->andCallOriginal();
        $double = $doubleBuilder->build();
        
        $this->assertEquals('variadic 123', $double->methodVariadic(1, 2, 3));
    }
    
    public function testInstanceWithOptionals()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('methodWithOptionals')
            ->with(1)
            ->andCallOriginal();
        $double = $doubleBuilder->build();
        
        $this->assertEquals('optional 12', $double->methodWithOptionals(1));
    }
    
    public function testInstanceMagicMethod()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('__call')
            ->with('methodName', ['value_1'])
            ->andReturn('return_value_1');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodName('value_1'));
    }
    
    public function testInstanceStaticMagicMethod()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('__callStatic')
            ->with('methodName', ['value_1'])
            ->andReturn('return_value_1');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double::methodName('value_1'));
    }
    
    public function testInstanceStatic()
    {
        $stdClass = new \stdClass();
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('staticMethod')
            ->with($stdClass)
            ->andReturn('return_value_1');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double::staticMethod($stdClass));
    }
    
    public function testInstanceStaticCallOriginal()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('staticMethodCallOriginal')
            ->with('value_1')
            ->andCallOriginal();
        $double = $doubleBuilder->build();
        
        $this->assertEquals('originalStatic value_1', $double::staticMethodCallOriginal('value_1'));
    }
    
    /**
     * @expectedException \Chmeldax\PhpMocks\Exceptions\UnexpectedCallException
     */
    public function testInstanceNonMatchingCall()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('methodCallback')
            ->with('value_1')
            ->andReturn('return_value_1');
        $double = $doubleBuilder->build();
        
        $double->methodCallback('value_2');
    }
    
     /**
     * @expectedException \Chmeldax\PhpMocks\Exceptions\InvalidDefinitionException
     */
    public function testInstanceTooManyArguments()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('methodConsecutive')
            ->with('value_1')
            ->andReturn('return_value_1');
    }
    
    /**
     * @expectedException \Chmeldax\PhpMocks\Exceptions\UnexpectedCallException
     */
    public function testInstanceTooMuchConsecutiveCalls()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('methodConsecutive')
            ->with()
            ->andReturn('return_value_1', 'return_value_2');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodConsecutive());
        $this->assertEquals('return_value_2', $double->methodConsecutive());
        $double->methodConsecutive();
    }
    
    /**
     * @expectedException \ReflectionException
     */
    public function testInstanceMissingMethod()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder->allowMethodCall('gibberish');
    }
    
    /**
     * @expectedException \Chmeldax\PhpMocks\Exceptions\InvalidDefinitionException
     */
    public function testInstancePrivateMethod()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder->allowMethodCall('methodPrivate');
    }
    
    /**
     * @expectedException \Chmeldax\PhpMocks\Exceptions\InvalidDefinitionException
     */
    public function testInstanceProtectedMethod()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder->allowMethodCall('methodProtected');
    }
    
    /**
     * @expectedException \Chmeldax\PhpMocks\Exceptions\InvalidDefinitionException
     */
    public function testInstanceConstructor()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder->allowMethodCall('__construct');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInstanceNotAllowedMethodCall()
    {
        $stdClass = new \stdClass();
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $double = $doubleBuilder->build();
        
        $double->methodWithTypeHint(null, null, $stdClass);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInstanceNotAllowedStaticMethodCall()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $double = $doubleBuilder->build();
        
        $double::staticMethod(null);
    }
    
    public function testInstanceIsA()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $double = $doubleBuilder->build();
        
        $this->assertTrue(is_a($double, 'Chmeldax\PhpMocks\TestFixtures\TestObject'));
    }
    
    public function testInstanceWithClassName()
    {
        $doubleBuilder = new \Chmeldax\PhpMocks\Doubles\Builder('Chmeldax\PhpMocks\TestFixtures\TestObject');
        $doubleBuilder
            ->allowMethodCall('methodCallback')
            ->with('return_value_1')
            ->andInvoke(function ($whatever) {
                return $whatever;
            });
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodCallback('return_value_1'));
    }
    
    public function testAbstractClassMethod()
    {
        $className = 'Chmeldax\PhpMocks\TestFixtures\TestAbstractObject';
        $doubleBuilder = new \Chmeldax\PhpMocks\Doubles\Builder($className);
        $doubleBuilder
            ->allowMethodCall('methodAbstract')
            ->with('value_1', 'value_2')
            ->andReturn('return_value_1');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodAbstract('value_1', 'value_2'));
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInstanceWithClassNameCallOriginal()
    {
        $doubleBuilder = new \Chmeldax\PhpMocks\Doubles\Builder('Chmeldax\PhpMocks\TestFixtures\TestObject');
        $doubleBuilder
            ->allowMethodCall('methodCallOriginal')
            ->with('return_value_1')
            ->andCallOriginal();
        $double = $doubleBuilder->build();
        
        $double->methodCallOriginal('return_value_1');
    }
    
    public function testInterfaceMethod()
    {
        $interfaceName = 'Chmeldax\PhpMocks\TestFixtures\TestInterface';
        $interfaceBuilder = new \Chmeldax\PhpMocks\Doubles\Builder($interfaceName);
        $interfaceBuilder
            ->allowMethodCall('method')
            ->with()
            ->andReturn('return_value_1');
        $interface = $interfaceBuilder->build();
        
        $this->assertEquals('return_value_1', $interface->method());
    }
    
    public function testInterfaceIsA()
    {
        $interfaceName = 'Chmeldax\PhpMocks\TestFixtures\TestInterface';
        $interfaceBuilder = new \Chmeldax\PhpMocks\Doubles\Builder($interfaceName);
        $interface = $interfaceBuilder->build();
        
        $this->assertTrue(is_a($interface, $interfaceName));
    }
    
    private function createInstanceDoubleBuilder()
    {
        $instance = new TestFixtures\TestObject;
        return new Doubles\Builder($instance);
    }
}
