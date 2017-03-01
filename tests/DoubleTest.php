<?php
namespace Chmeldax\PhpMocks;

require_once __DIR__ . '/../vendor/autoload.php';

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
            ->andInvoke(function($whatever) {
                return $whatever; 
            });
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodCallback('return_value_1'));
    }
    
    /**
     * @expectedException \Chmeldax\PhpMocks\TestException
     */
    public function testInstanceException()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder
            ->allowMethodCall('methodException')
            ->with()
            ->andThrow(new TestException);
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
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $double = $doubleBuilder->build();
        
        $double->methodWithTypeHint(null, null, null);
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
        
        $this->assertTrue(is_a($double, 'Chmeldax\PhpMocks\TestingObject'));
    }
    
    public function testInstanceWithClassName()
    {
        $doubleBuilder = new \Chmeldax\PhpMocks\Doubles\Builder('Chmeldax\PhpMocks\TestingObject');
        $doubleBuilder
            ->allowMethodCall('methodCallback')
            ->with('return_value_1')
            ->andInvoke(function($whatever) {
                return $whatever; 
            });
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodCallback('return_value_1'));    
    }
    
    public function testAbstractClassMethod()
    {
        $className = 'Chmeldax\PhpMocks\TestingAbstractObject';
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
        $doubleBuilder = new \Chmeldax\PhpMocks\Doubles\Builder('Chmeldax\PhpMocks\TestingObject');
        $doubleBuilder
            ->allowMethodCall('methodCallOriginal')
            ->with('return_value_1')
            ->andCallOriginal();
        $double = $doubleBuilder->build();
        
        $double->methodCallOriginal('return_value_1');
    }
    
    public function testInterfaceMethod()
    {
        $interfaceName = 'Chmeldax\\PhpMocks\\TestingInterface';
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
        $interfaceName = 'Chmeldax\\PhpMocks\\TestingInterface';
        $interfaceBuilder = new \Chmeldax\PhpMocks\Doubles\Builder($interfaceName);
        $interface = $interfaceBuilder->build();
        
        $this->assertTrue(is_a($interface, $interfaceName));
    }
    
    private function createInstanceDoubleBuilder()
    {
        $instance = new TestingObject;
        return new \Chmeldax\PhpMocks\Doubles\Builder($instance);
    }
}

class TestingObject 
{
    public function __construct()
    {
        
    }
    
    public function methodWithTypeHint($a, $b, \stdClass $c)
    {
        
    }
    
    public function methodConsecutive()
    {
        
    }
    
    public function methodCallback($a)
    {
        
    }
    
    public function methodException()
    {
        
    }
    
    public function methodCallOriginal($a)
    {
        return 'original ' . $a;
    }
    
    public function methodVariadic($a, ...$variadic)
    {
        return 'variadic ' . $a . $variadic[0] . $variadic[1];
    }
    
    public function methodWithOptionals($a, $b = '2')
    {
        return 'optional ' . $a . $b;
    }
    
    public static function staticMethod(\stdClass $a)
    {
        
    }
    
    public static function staticMethodCallOriginal($a)
    {
        return 'originalStatic ' . $a;
    }
    
    public function __call($name, $arguments)
    {
        
    }
    
    public static function __callStatic($name, $arguments)
    {
        
    }
    
    private function methodPrivate()
    {
        
    }
    
    protected function methodProtected()
    {
        
    }
}

abstract class TestingAbstractObject
{
    abstract public function methodAbstract($a, $b);
}

interface TestingInterface
{
    public function method();
}

class TestException extends \Exception
{
    
}
