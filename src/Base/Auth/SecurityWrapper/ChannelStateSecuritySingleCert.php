<?php


namespace Base\Auth\SecurityWrapper;


use Base\Auth\CryptoProcessor\CryptoProcessorInterface;
use Base\Auth\StateProvider\StateProviderInterface;

class ChannelStateSecuritySingleCert implements ChannelStateSecurityWrapperInterface
{
    /**
     * @var StateProviderInterface
     */
    private $stateProvider;

    /**
     * @var CryptoProcessorInterface
     */
    private $cryptoProcessor;

    private $defaultCertName = 'main';

    const POSTFIX_CERT_NAME = '_cert';
    const POSTFIX_SIGNATURE = '_sign';

    public function setCertName($certName)
    {
        $this->defaultCertName = $certName;
    }

    public function setStateProvider(StateProviderInterface $stateProvider)
    {
        $this->stateProvider = $stateProvider;
    }

    public function setCryptoProcessor(CryptoProcessorInterface $cryptoProcessor)
    {
        $this->cryptoProcessor = $cryptoProcessor;
    }

    public function getState($key, $default = null)
    {
        $state = $this->stateProvider->getState($key);
        if ($state === null) {
            return $default;
        }

        $certName = $this->stateProvider->getState($key . self::POSTFIX_CERT_NAME);
        $signature = $this->stateProvider->getState($key . self::POSTFIX_SIGNATURE);
        
        if (!$certName || !$signature) {
            return $default;
        }
        
        if (!$this->cryptoProcessor->checkSignature($state, $signature, $certName)) {
            return $default;
        }
        
        return $state;
    }

    public function setState($key, $state, $ttl)
    {
        $certNameKey = $key . self::POSTFIX_CERT_NAME;
        $signatureKey = $key . self::POSTFIX_SIGNATURE;

        if ($state !== null) {
            $this->stateProvider->setState($signatureKey, $this->cryptoProcessor->getSignature($state, $this->defaultCertName), $ttl);
            $this->stateProvider->setState($certNameKey, $this->defaultCertName, $ttl);
            $this->stateProvider->setState($key, $state, $ttl);    
        } else {
            $this->stateProvider->setState($signatureKey, null, $ttl);
            $this->stateProvider->setState($certNameKey, null, $ttl);
            $this->stateProvider->setState($key, null, $ttl);
        }
        
        return $state;
    }
}