<?php
namespace Chmeldax\PhpMocks\Methods;

class AllowedMethod extends Method
{   
    /**
     * @param mixed $constraints
     * @return \Chmeldax\PhpMocks\Branches\Branch
     */
    public function with(...$constraints)
    {
        $branch = parent::with(...$constraints);
        return $branch->anytime();
    }
}
