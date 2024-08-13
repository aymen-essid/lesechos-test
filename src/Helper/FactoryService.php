<?php

namespace App\Helper;

class FactoryService
{
    public static function createInstance()
    {
        return new MyClass();
    }
}