<?php
namespace PhpMocks\Methods;

class AllowedMethod extends Method
{   
    /**
     * @param mixed $constraints
     * @return \PhpMocks\Branches\Branch
     */
    public function with(...$constraints)
    {
        $branch = parent::with(...$constraints);
        return $branch->anytime();
    }
}
