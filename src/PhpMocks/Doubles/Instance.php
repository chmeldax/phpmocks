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
    
    public function __call($name, $arguments)
    {
        if(!isset($this->methods[$name])) {
            throw new \Exception("Unexpected call to $name");
        }
        return $this->methods[$name]->performCall($arguments);
    }
    
    public static function __callStatic($name, $arguments) {
        throw new \InvalidArgumentException('Mocking of static methods is not supported.');
    }
}

