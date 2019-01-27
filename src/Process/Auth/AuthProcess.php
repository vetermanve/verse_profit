<?php


namespace Process\Auth;


use Process\Auth\Strategies\SelectUserCurrentBudget;
use Verse\Modular\ModularProcessor;

class AuthProcess
{
    /**
     * @param AuthProcessContext $processContext
     *
     * @return ModularProcessor
     */
    public function getAuthProcess (AuthProcessContext $processContext) 
    {
        $processor = new ModularProcessor();
        $processor->setContext($processContext);
        $processor->setContainer(new AuthProcessContainer());
        
        $processor->addStrategy(new SelectUserCurrentBudget(), $processor::SECTION_RUN);
        
        return $processor;
    }    
}