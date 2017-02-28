<?php
namespace PhpMocks\Doubles;

class Instance
{
    /** @var \PhpMocks\Methods\Method[] */
    private $methods;
    
    /** @var \PhpMocks\Methods\Method[] */
    private static $staticMethods;
    
    /**
     * 
     * @param array $methods
     * @param array $staticMethods
     */
    public function __construct(array $methods, array $staticMethods)
    {
        $this->methods = $methods;
        self::$staticMethods = $staticMethods;
    }
    
    private function callMethod($methodName, array $arguments)
    {
        if(array_key_exists($methodName, $this->methods)) {
            return $this->methods[$methodName]->performCall($arguments);
        }
        
        throw new \InvalidArgumentException('Unexpected call.');
    }
    
    private static function callStaticMethod($methodName, array $arguments)
    {
        if(array_key_exists($methodName, self::$staticMethods)) {
            return self::$staticMethods[$methodName]->performCall($arguments);
        }
        
        throw new \InvalidArgumentException('Unexpected call.');
    }
}
