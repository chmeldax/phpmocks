<?php
namespace Chmeldax\PhpMocks;

class ExpectedMethodTest extends \PHPUnit_Framework_TestCase
{
    public function testTimesSuccess()
    {
        $stdClass = new \stdClass();
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        
        $doubleBuilder
            ->expectMethodCall('methodWithTypeHint')
            ->with(new Constraints\Anything, new Constraints\Anything, $stdClass)
            ->times(1)
            ->andReturn('return_value_1');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodWithTypeHint('str1', 'str2', $stdClass));
        $this->assertTrue($doubleBuilder->checkExpectations());
    }
    
    /**
     * @expectedException \Chmeldax\PhpMocks\Exceptions\ExpectationNotMetException
     */
    public function testTimesFailing()
    {
        $stdClass = new \stdClass();
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        
        $doubleBuilder
            ->expectMethodCall('methodWithTypeHint')
            ->with(new Constraints\Anything, new Constraints\Anything, $stdClass)
            ->times(10)
            ->andReturn('return_value_1');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodWithTypeHint('str1', 'str2', $stdClass));
        $doubleBuilder->checkExpectations();
    }
    
    public function testNeverSuccess()
    {
        $stdClass = new \stdClass();
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        
        $doubleBuilder
            ->expectMethodCall('methodWithTypeHint')
            ->with(new Constraints\Anything, new Constraints\Anything, $stdClass)
            ->never()
            ->andReturn('return_value_1');
        
        $this->assertTrue($doubleBuilder->checkExpectations());
    }
    
    /**
     * @expectedException \Chmeldax\PhpMocks\Exceptions\ExpectationNotMetException
     */
    public function testNeverFailing()
    {
        $stdClass = new \stdClass();
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        
        $doubleBuilder
            ->expectMethodCall('methodWithTypeHint')
            ->with(new Constraints\Anything, new Constraints\Anything, $stdClass)
            ->never()
            ->andReturn('return_value_1');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodWithTypeHint('str1', 'str2', $stdClass));
        $doubleBuilder->checkExpectations();
    }
    
    public function testAtCallsSuccess()
    {
        $stdClassA = new \stdClass();
        $stdClassB = new \stdClass();
        $stdClassB->foo = 'bar';
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        
        $doubleBuilder
            ->expectMethodCall('methodWithTypeHint')
            ->with(new Constraints\Anything, new Constraints\Anything, $stdClassA)
            ->atCalls(1, 3)
            ->andReturn('return_value_1');
        $doubleBuilder
            ->expectMethodCall('methodWithTypeHint')
            ->with(new Constraints\Anything, new Constraints\Anything, $stdClassB)
            ->atCall(2)
            ->andReturn('return_value_2');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodWithTypeHint('str1', 'str2', $stdClassA));
        $this->assertEquals('return_value_2', $double->methodWithTypeHint('str1', 'str2', $stdClassB));
        $this->assertEquals('return_value_1', $double->methodWithTypeHint('str1', 'str2', $stdClassA));
        $this->assertTrue($doubleBuilder->checkExpectations());
    }
    
    /**
     * @expectedException \Chmeldax\PhpMocks\Exceptions\ExpectationNotMetException
     */
    public function testAtCallsFailing()
    {
        $stdClassA = new \stdClass();
        $stdClassB = new \stdClass();
        $stdClassB->foo = 'bar';
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        
        $doubleBuilder
            ->expectMethodCall('methodWithTypeHint')
            ->with(new Constraints\Anything, new Constraints\Anything, $stdClassA)
            ->atCalls(1, 2)
            ->andReturn('return_value_1');
        $doubleBuilder
            ->expectMethodCall('methodWithTypeHint')
            ->with(new Constraints\Anything, new Constraints\Anything, $stdClassB)
            ->atCall(3)
            ->andReturn('return_value_2');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodWithTypeHint('str1', 'str2', $stdClassA));
        $this->assertEquals('return_value_2', $double->methodWithTypeHint('str1', 'str2', $stdClassB));
        $this->assertEquals('return_value_2', $double->methodWithTypeHint('str1', 'str2', $stdClassB));
        $doubleBuilder->checkExpectations();
    }
    
    public function testAnytimeSuccess()
    {
        $stdClass = new \stdClass();
        $doubleBuilder = $this->createInstanceDoubleBuilder();
        
        $doubleBuilder
            ->expectMethodCall('methodWithTypeHint')
            ->with(new Constraints\Anything, new Constraints\Anything, $stdClass)
            ->anytime()
            ->andReturn('return_value_1');
        $double = $doubleBuilder->build();
        
        $this->assertEquals('return_value_1', $double->methodWithTypeHint('str1', 'str2', $stdClass));
        $this->assertTrue($doubleBuilder->checkExpectations());
    }
    
    private function createInstanceDoubleBuilder()
    {
        $instance = new TestFixtures\TestObjectExpectations;
        return new Doubles\Builder($instance);
    }
}
