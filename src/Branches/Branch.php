<?php
namespace Chmeldax\PhpMocks\Branches;

use Chmeldax\PhpMocks\Expectations\CallExpectation;
use Chmeldax\PhpMocks\Expectations\CountExpectation;

class Branch
{
    /** @var \Chmeldax\PhpMocks\Constraints\Constraint[] */
    private $constraints;
    
    /** @var \ReflectionMethod */
    private $methodReflection;
    
    /** @var Chmeldax\PhpMocks\Expectations\Expectation */
    private $expectation;
    
    /**
     * 
     * @param array $constraints
     * @param \ReflectionMethod $methodReflection
     * @param object|null $instance
     */
    public function __construct(array $constraints, \ReflectionMethod $methodReflection, $instance)
    {
        $this->constraints = $constraints;
        $this->methodReflection = $methodReflection;
        $this->instance = $instance;
    }
    
    /**
     * @param array $parameters
     */
    public function isEligible(array $parameters)
    {
        if(count($this->constraints) !== count($parameters)) {
            return false;
        }
        $parameter = current($parameters);
        foreach($this->constraints as $constraint) {
            if(!$constraint->checkValue($parameter)) {
                return false;
            }
            $parameter = next($parameters);
        }
        return true;
    }
    
    /**
     * @param array $parameters
     * @param integer $callNumber
     * @return mixed
     * @throws \Exception
     */
    public function performCall(array $parameters, $callNumber)
    {
        return $this->expectation->performCall($parameters, $callNumber);
    }
    
    /**
     * @return CountExpectation
     */
    public function once()
    {
        return $this->times(1);
    }

    /**
     * @param integer $count
     * @return CountExpectation
     */
    public function times($count)
    {
        $expectation = new CountExpectation($this->methodReflection, $this->instance, $count);
        $this->expectation = $expectation;
        return $expectation;
    }
    
    /**
     * @return CountExpectation
     */
    public function never()
    {
        return $this->times(0);
    }
    
    /**
     * @return CallExpectation
     */
    public function anytime()
    {
        $expectation = new CallExpectation($this->methodReflection, $this->instance, null);
        $this->expectation = $expectation;
        return $expectation;
    }
    
    /**
     * @param integer $callNumber
     * @return CallExpectation
     */
    public function atCall($callNumber)    
    {
        return $this->atCalls($callNumber);
    }
    
    /**
     * @param integer $callNumbers
     * @return CallExpectation
     */
    public function atCalls(...$callNumbers)
    {
        $expectation = new CallExpectation($this->methodReflection, $this->instance, $callNumbers);
        $this->expectation = $expectation;
        return $expectation;
    }
    
    public function checkExpectation()
    {
        return $this->expectation->isExpectationMet();
    }
}

