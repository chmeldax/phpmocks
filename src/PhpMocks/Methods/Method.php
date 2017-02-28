<?php
namespace PhpMocks\Methods;

class Method
{
    /** @var \PhpMocks\Branches\Branch[] */
    private $branches = [];
    
    /** @var \PhpMocks\Branches\Builder */
    private $branchBuilder;
    
    /**
     * 
     * @param \ReflectionMethod $methodReflection
     * @param object|null $instance
     */
    public function __construct(\ReflectionMethod $methodReflection, $instance)
    {
        $this->branchBuilder = new \PhpMocks\Branches\Builder($methodReflection, $instance);
    }
    
    public function with(...$constraints)
    {
        $branch = $this->branchBuilder->build($constraints);
        $this->branches[] = $branch;
        return $branch;
    }
    
    public function performCall($arguments)
    {
        foreach(array_reverse($this->branches) as $branch) {
            if($branch->isEligible($arguments)) {
                return $branch->performCall($arguments);
            }
        }
        throw new \Exception('There is no block available');
    }
}

