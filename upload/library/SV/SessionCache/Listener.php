<?php

class SV_SessionCache_Listener
{
    public static function load_class($class, array &$extend)
    {
        $extend[] = 'SV_SessionCache_'.$class;
    }
}