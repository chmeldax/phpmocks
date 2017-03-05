<?php
namespace Chmeldax\PhpMocks\Expectations;

use Chmeldax\PhpMocks\Exceptions\ExpectationNotMetException;

class CallExpectation extends Expectation
{
    /** @var array|null */
    private $expectedCallNumbers;
    
    /** @var array */
    private $callNumbers = [];
    
    /**
     * @param \ReflectionMethod $reflectionMethod
     * @param object|null $instance
     * @param integer|null $expectedCallNumbers
     */
    public function __construct(\ReflectionMethod $reflectionMethod, $instance, $expectedCallNumbers)
    {
        $this->expectedCallNumbers = $expectedCallNumbers;
        parent::__construct($reflectionMethod, $instance);
    }
    
    /**
     * @param array $parameters
     * @param integer $callNumber
     */
    public function performCall(array $parameters, $callNumber)
    {
        $this->callNumbers[] = $callNumber;
        return parent::performCall($parameters, $callNumber);
    }
    
    /**
     * @return boolean
     */
    public function isExpectationMet()
    {
        if (is_null($this->expectedCallNumbers)) {
            return true;
        }
        
        if ($this->expectedCallNumbers != $this->callNumbers) {
            $message = 'Expected method %s to be called at calls number %s ' .
                       'instead of %s.';
            throw new ExpectationNotMetException(
                sprintf(
                    $message,
                    $this->methodReflection->getName(),
                    implode(', ', $this->expectedCallNumbers),
                    implode(', ', $this->callNumbers)
                )
            );
        }
    }
}
