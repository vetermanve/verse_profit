<?php


namespace Base\Auth\StateProvider;


use Verse\Run\Interfaces\HttpRequestDataWrapperInterface;

class RunRequestWrapperStateProvider implements StateProviderInterface
{
    private $runRequestWrapper;

    /**
     * RunRequestWrapperStateProvider constructor.
     * 
     * @param $runRequestWrapper
     */
    public function __construct(HttpRequestDataWrapperInterface $runRequestWrapper)
    {
        $this->runRequestWrapper = $runRequestWrapper;
    }

    public function getState($key, $default = null)
    {
        return $this->runRequestWrapper->getState($key, $default);
    }

    public function setState($key, $value, $ttl)
    {
        return $this->runRequestWrapper->setState($key, $value, $ttl);
    }
}