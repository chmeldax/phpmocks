<?php
namespace PhpMocks\Expectations;

class CountExpectation extends Expectation
{
    /** @var integer */
    private $expectedNumberOfCalls;
    
    /** @var integer */
    private $numberOfCalls = 0;
    
    /**
     * @param \ReflectionMethod $reflectionMethod
     * @param object|null $instance
     * @param integer $expectedNumberOfCalls
     */
    public function __construct(\ReflectionMethod $reflectionMethod, $instance, $expectedNumberOfCalls)
    {
        $this->expectedNumberOfCalls = $expectedNumberOfCalls;
        parent::__construct($reflectionMethod, $instance);
    }
    
    /**
     * @param array $parameters
     * @param integer $callNumber
     */
    public function performCall(array $parameters, $callNumber)
    {
        $this->numberOfCalls++;
        return parent::performCall($parameters, $callNumber);
    }
    
    /**
     * @return boolean
     */
    public function isExpectationMet()
    {
        if(is_null($this->expectedNumberOfCalls)) {
            return true;
        }
        if($this->expectedNumberOfCalls !== $this->numberOfCalls) {
            $message = 'Expected %s calls for method %s, got %s calls.';
            throw new ExpectationNotMetException(
                sprintf(
                    $message,
                    $this->expectedNumberOfCalls,
                    $this->methodReflection->getName(),
                    $this->numberOfCalls
                )
            );
        }
        return true;
    }
}
