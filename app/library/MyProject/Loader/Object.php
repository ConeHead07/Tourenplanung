<?php

class MyProject_Loader_Object
{
    public static function getClass($class)
    {
        return new $class;
    }
}
