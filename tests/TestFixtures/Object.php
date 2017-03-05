<?php
namespace Chmeldax\PhpMocks\TestFixtures;

class Object
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
