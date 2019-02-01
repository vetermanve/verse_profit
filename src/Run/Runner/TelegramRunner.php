<?php


namespace Run\Runner;


use Run\Channel\TelegramBotChannel;
use Run\Component\Configuration;
use Run\Processor\TelegramProcessor;
use Run\Provider\TelegramProvider;
use Verse\Run\Component\CreateDependencyContainer;
use Verse\Run\Component\UnexpectedShutdownHandler;
use Verse\Run\RunContext;
use Verse\Run\RunCore;
use Verse\Run\RuntimeLog;

class TelegramRunner extends RunnerProto
{
    public function run () 
    {
        $runtime = new RuntimeLog();
        $runtime->catchErrors();
        
        $context = new RunContext();
        
        $core = new RunCore();
        $core->setContext($context);
        $core->setRuntime($runtime);
    
        $core->addComponent(new UnexpectedShutdownHandler());
        $core->addComponent(new CreateDependencyContainer());
        $core->addComponent(new Configuration());
        
        foreach ($this->getDafaultComponents() as $component) {
            $core->addComponent($component);   
        }
        
        $provider = new TelegramProvider();
        $core->setProvider($provider);
        
        $channel = new TelegramBotChannel();
        $core->setDataChannel($channel);
        
        $processor = new TelegramProcessor();
        $core->setProcessor($processor);
        
        $core->configure();
        $core->prepare();
        $core->run();
    }
}