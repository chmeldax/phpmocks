<?php
namespace Chmeldax\PhpMocks\Expectations;

use \Chmeldax\PhpMocks\Exceptions\UnexpectedCallException;

class Expectation
{
    /** @var integer */
    private $numberOfCalls = 0;
    
    /** @var object|null */
    private $instance;
    
    /** @var array */
    private $returnValues;
    
    /** @var $callback */
    private $callback;
    
    /** @var \Exception */
    private $exception;
    
    /** @var \ReflectionMethod */
    protected $methodReflection;
    
    /**
     * @param \ReflectionMethod $methodReflection
     * @param object|null $instance
     */
    public function __construct(\ReflectionMethod $methodReflection, $instance)
    {
        $this->methodReflection = $methodReflection;
        $this->instance = $instance;
    } 
    
    /**
     * @return boolean
     */
    public function isExpectationMet()
    {
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
        if($this->callOriginal) {
            return $this->methodReflection->invokeArgs($this->instance, $parameters);
        } elseif($this->callback) {
            return call_user_func_array($this->callback, $parameters);
        } elseif ($this->exception) {
            throw $this->exception;
        }
        return $this->getReturnValue();
    }
    
    /**
     * 
     * @param mixed $returnValues
     * @throws \Exception
     */
    public function andReturn(...$returnValues)
    {
        if($this->callback) {
            throw new \Exception('You cannot use both Invoke and Return');
        }
        $this->returnValues = $returnValues;
    }
    
    /**
     * @param callable $callback
     * @throws \Exception
     */
    public function andInvoke(callable $callback)
    {
        if($this->returnValues) {
            throw new \Exception('You cannot use both Invoke and Return');
        }
        $this->callback = $callback;
    }
    
    /**
     * @param \Exception $exception
     */
    public function andThrow(\Exception $exception)
    {
        //Check for others
        $this->exception = $exception;
    }
    
    /**
     * @throws \InvalidArgumentException
     */
    public function andCallOriginal()
    {
        if(is_null($this->instance)) {
            throw new \InvalidArgumentException;
        }
        $this->callOriginal = true;
    }
    
    private function getReturnValue()
    {
        if(count($this->returnValues) === 1) {
            return $this->returnValues[0];
        }
        
        if(!array_key_exists($this->numberOfCalls, $this->returnValues)) {
            $message = 'There are not enough return values for consecutive ' .
                        'calls!';
            throw new UnexpectedCallException($message);
        }
        $returnValue = $this->returnValues[$this->numberOfCalls];
        $this->numberOfCalls++;
        return $returnValue;
    }
}
