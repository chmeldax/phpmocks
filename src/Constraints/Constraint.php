<?php
namespace Chmeldax\PhpMocks\Constraints;

interface Constraint
{
    /**
     *
     * @param mixed $actualValue
     */
    public function checkValue($actualValue);
    
    /**
     *
     * @param \ReflectionParameter $reflectionParameter
     */
    public function checkCompliance(\ReflectionParameter $reflectionParameter);
}
