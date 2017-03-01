<?php
namespace PhpMocks\Methods;

use \PhpMocks\Exceptions\UnexpectedCallException;

class Method
{
    /** @var \PhpMocks\Branches\Branch[] */
    private $branches = [];
    
    /** @var \PhpMocks\Branches\Builder */
    private $branchBuilder;
    
    /** @var \ReflectionMethod */
    private $methodReflection;
    
    /** @var integer */
    private $callNumber = 0;
    
    /**
     * @param \ReflectionMethod $methodReflection
     * @param object|null $instance
     */
    public function __construct(\ReflectionMethod $methodReflection, $instance)
    {
        $this->methodReflection = $methodReflection;
        $this->branchBuilder = new \PhpMocks\Branches\Builder(
            $methodReflection,
            $instance
        );
    }
    
    /**
     * @param mixed $constraints
     * @return \PhpMocks\Branches\Branch
     */
    public function with(...$constraints)
    {
        $branch = $this->branchBuilder->build($constraints);
        $this->branches[] = $branch;
        return $branch;
    }
    
    public function checkExpectations()
    {
        foreach($this->branches as $branch) {
            $branch->checkExpectation();
        }
        return true;
    }
    
    
    /**
     * @param mixed $arguments
     * @return mixed
     * @throws \Exception
     */
    public function performCall($arguments)
    {
        $this->callNumber++;
        foreach(array_reverse($this->branches) as $branch) {
            if($branch->isEligible($arguments)) {
                return $branch->performCall($arguments, $this->callNumber);
            }
        }
        
        $message = 'There is no matching definition for this call!  Please, ' .
                   'check you with() blocks.';
        throw new UnexpectedCallException($message);
    }
    
    /**
     * @return boolean
     */
    public function isStatic()
    {
        return $this->methodReflection->isStatic();
    }
}

