<?php


namespace Base\Auth;


use Base\Auth\CryptoProcessor\CryptoProcessorInterface;
use Base\Auth\SecurityWrapper\ChannelStateSecuritySingleCert;
use Base\Auth\StateProvider\StateProviderInterface;
use Verse\Run\Util\ChannelState;

class ChannelSecurityWrapperFactory
{
    protected $defaultCert = 'main';

    /**
     * @var \Verse\Di\ContainerInterface
     */
    protected $container;
    
    public function getWrapper (StateProviderInterface $stateProvider) 
    {
        $channelStateWrapper = new ChannelStateSecuritySingleCert();
        $channelStateWrapper->setCertName($this->defaultCert);
        $channelStateWrapper->setCryptoProcessor($this->container->bootstrap(CryptoProcessorInterface::class));
        $channelStateWrapper->setStateProvider($stateProvider);
        
        return $channelStateWrapper;
    }

    /**
     * @return string
     */
    public function getDefaultCert() : string
    {
        return $this->defaultCert;
    }

    /**
     * @param string $defaultCert
     */
    public function setDefaultCert(string $defaultCert)
    {
        $this->defaultCert = $defaultCert;
    }

    /**
     * @return \Verse\Di\ContainerInterface
     */
    public function getContainer() : \Verse\Di\ContainerInterface
    {
        return $this->container;
    }

    /**
     * @param \Verse\Di\ContainerInterface $container
     */
    public function setContainer(\Verse\Di\ContainerInterface $container)
    {
        $this->container = $container;
    }
}