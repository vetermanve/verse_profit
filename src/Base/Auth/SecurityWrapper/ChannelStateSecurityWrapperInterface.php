<?php


namespace Base\Auth\SecurityWrapper;


use Base\Auth\CryptoProcessor\CryptoProcessorInterface;
use Base\Auth\StateProvider\StateProviderInterface;

interface ChannelStateSecurityWrapperInterface
{
    public function setStateProvider (StateProviderInterface $channelState);
    public function setCryptoProcessor (CryptoProcessorInterface $cryptoProcessor); 
    public function getState ($key);
    public function setState ($key, $value, $ttl);
}