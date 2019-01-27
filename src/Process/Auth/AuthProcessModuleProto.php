<?php


namespace Process\Auth;


use Verse\Modular\ModularContainerProto;
use Verse\Modular\ModularContextProto;
use Verse\Modular\ModularSystemModule;

class AuthProcessModuleProto implements ModularSystemModule
{
    /**
     * @var AuthProcessContainer 
     */
    protected $container;
    
    /**
     * @var AuthProcessContext
     */
    protected $context;
    
    /**
     * @return AuthProcessContainer
     */
    final public function getContainer(): AuthProcessContainer
    {
        return $this->container;
    }
    
    /**
     * @param AuthProcessContainer $container
     */
    final public function setContainer($container): void
    {
        $this->container = $container;
    }
    
    /**
     * @return AuthProcessContext
     */
    final public function getContext(): AuthProcessContext
    {
        return $this->context;
    }
    
    /**
     * @param AuthProcessContext $context
     */
    final public function setContext($context): void
    {
        $this->context = $context;
    }
}