<?php
namespace PhpMocks;

require_once __DIR__ . '/../../vendor/autoload.php';

class TestingObject 
{
    public function testMethod($a, $b, \stdClass $c) {}
    public function testMethod2($a) {}
    public function whatever() {}
    public function whatever2($a) {
        return 'original ' . $a;
    }
    public static function staticMethod() {}
    public static function staticMethod2(\stdClass $a) {}
    public static function staticMethod3($a) {
        return 'originalStatic ' . $a;
    }
}

class Exception extends \Exception
{
    
}

class ConceptTest extends \PHPUnit_Framework_TestCase
{
    public function testMock()
    {
        $stdClass = new \stdClass;
        $className = 'PhpMocks\TestingObject';
        $instance = new TestingObject;
        $doubleBuilder = new \PhpMocks\Doubles\Builder($instance);
        $doubleBuilder
            ->allowMethodCall('testMethod')
            ->with(new Constraints\Anything, new Constraints\Anything, $stdClass)
            ->andReturn('hello', 'hello2');
        $doubleBuilder->allowMethodCall('testMethod2')->with(1)->andInvoke(function($whatever) {
            return $whatever; 
        });
        $doubleBuilder->allowMethodCall('whatever')->with()->andThrow(new Exception);
        $doubleBuilder->allowMethodCall('whatever2')->with('haha')->andCallOriginal();
        $doubleBuilder->allowMethodCall('staticMethod2')->with($stdClass)->andReturn('helloA');
        $doubleBuilder->allowMethodCall('staticMethod3')->with('staticHaha')->andCallOriginal();
                
        $double = $doubleBuilder->build();
        
        $this->assertEquals('hello', $double->testMethod('hehe', 'hehe', $stdClass));
        $this->assertEquals('hello2', $double->testMethod('hehe', 'hehe', $stdClass));
        $this->assertEquals(1, $double->testMethod2(1));
        try {
            $double->whatever();
            $this->fail('Exception not thrown');
        } catch (Exception $ex) {
            
        }
        
        $this->assertEquals('original haha', $double->whatever2('haha'));
        try {
            $double::staticMethod();
            $this->fail('Exception not thrown');
        } catch (\InvalidArgumentException $ex) {
        }
        $this->assertEquals('helloA', $double::staticMethod2($stdClass));
        $this->assertEquals('originalStatic staticHaha', $double::staticMethod3('staticHaha'));
        $this->assertTrue(is_a($double, $className));
    }
}

