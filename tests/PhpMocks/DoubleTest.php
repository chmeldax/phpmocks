<?php
namespace PhpMocks;

require_once __DIR__ . '/../../vendor/autoload.php';

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
     * @expectedException \PhpMocks\TestException
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
     * @expectedException \ReflectionException
     */
    public function testInstanceMissingMethod()
    {
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        $doubleBuilder->allowMethodCall('gibberish');
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
        
        $this->assertTrue(is_a($double, 'PhpMocks\TestingObject'));
    }
    
    public function testInstanceWithClassName()
    {
        $doubleBuilder = new \PhpMocks\Doubles\Builder('PhpMocks\TestingObject');
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
     * @expectedException \InvalidArgumentException
     */
    public function testInstanceWithClassNameCallOriginal()
    {
        $doubleBuilder = new \PhpMocks\Doubles\Builder('PhpMocks\TestingObject');
        $doubleBuilder
            ->allowMethodCall('methodCallOriginal')
            ->with('return_value_1')
            ->andCallOriginal();
        $double = $doubleBuilder->build();
        
        $double->methodCallOriginal('return_value_1');
    }
    
    public function testInterfaceMethod()
    {
        $interfaceName = 'PhpMocks\\TestingInterface';
        $interfaceBuilder = new \PhpMocks\Doubles\Builder($interfaceName);
        $interfaceBuilder
            ->allowMethodCall('method')
            ->with()
            ->andReturn('return_value_1');
        $interface = $interfaceBuilder->build();
        
        $this->assertEquals('return_value_1', $interface->method());
    }
    
    public function testInterfaceIsA()
    {
        $interfaceName = 'PhpMocks\\TestingInterface';
        $interfaceBuilder = new \PhpMocks\Doubles\Builder($interfaceName);
        $interface = $interfaceBuilder->build();
        
        $this->assertTrue(is_a($interface, $interfaceName));
    }
    
    private function createInstanceDoubleBuilder()
    {
        $instance = new TestingObject;
        return new \PhpMocks\Doubles\Builder($instance);
    }
}

class TestingObject 
{
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
}

interface TestingInterface
{
    public function method();
}

class TestException extends \Exception
{
    
}
