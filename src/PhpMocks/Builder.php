<?php
namespace PhpMocks;

class Builder
{
    public static function double($className, $instance = null)
    {
        return new Double($className, $instance);
    }
}

