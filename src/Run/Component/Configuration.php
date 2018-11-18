<?php


namespace Run\Component;


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
        
        Env::getContainer()->setModule(RunContext::class, $this->context);
    }
}