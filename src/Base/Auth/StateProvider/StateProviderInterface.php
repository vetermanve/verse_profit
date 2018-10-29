<?php


namespace Base\Auth\StateProvider;


interface StateProviderInterface
{
    public function getState ($key, $default = null);
    public function setState ($key, $value, $ttl); 
}