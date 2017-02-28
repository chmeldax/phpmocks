<?php
namespace PhpMocks\Doubles;

class Instance
{
    /** @var \PhpMocks\Methods\Method[] */
    private $methods;
    
    /**
     * 
     * @param \ReflectionClass $reflection
     * @param mixed $originalInstance
     */
    public function __construct(array $methods)
    {
        $this->methods = $methods;
    }
    
    private function callMethod($methodName, array $arguments)
    {
        if(array_key_exists($methodName, $this->methods)) {
            return $this->methods[$methodName]->performCall($arguments);
        }
        
        throw new \InvalidArgumentExcepton('Unexpected call.');
    }
    
    private static function callStaticMethod()
    {
        throw new \InvalidArgumentException('Unexpected call.');
    }
}
