<?php


namespace Run\Component;


use Psr\Log\LoggerInterface;
use Verse\Di\Env;
use Verse\Run\Component\RunComponentProto;
use Verse\Run\RunContext;

class Configuration extends RunComponentProto
{
    const SCOPE_KEY = 'config';
    
    const JBASE_ROOT = 'jbase_root';
    const JBASE_DB = 'jbase_db';
    
    public function run()
    {
        $config = [
            self::JBASE_DB => 'goals',
            self::JBASE_ROOT => '/var/www/data'
        ];
        
        $this->context->set('config', $config);
        $this->context->set('env', $_ENV);
        
        Env::getContainer()->setModule(RunContext::class, $this->context);
        Env::getContainer()->setModule(LoggerInterface::class, $this->runtime);
    }
}