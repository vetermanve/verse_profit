<?php


namespace Base\Storage;


use Run\Component\Configuration;
use Verse\Di\Env;
use Verse\Run\RunContext;
use Verse\Storage\Data\JBaseDataAdapter;
use Verse\Storage\SimpleStorage;
use Verse\Storage\StorageContext;
use Verse\Storage\StorageDependency;

abstract class GoalsBasicStorage extends SimpleStorage
{
    const ROOT = 'root';
    const DB = 'db';

    public function loadConfig()
    {
        /* @var  $globalContext RunContext */
        $globalContext = Env::getContainer()->bootstrap(RunContext::class);

        $this->context->set(
            self::ROOT,
            $globalContext->getScope(Configuration::SCOPE_KEY, Configuration::JBASE_ROOT)
        );

        $this->context->set(
            self::DB,
            $globalContext->getScope(Configuration::SCOPE_KEY, Configuration::JBASE_DB)
        );
    }

    public function customizeDi(StorageDependency $container, StorageContext $context)
    {
        $adapter = new JBaseDataAdapter();
        $adapter->setDatabase($this->context->get(self::DB, 'local-database'));
        $adapter->setDataRoot($this->context->get(self::ROOT, 'jbase'));
        $adapter->setResource($this->getTableName());
        $adapter->setPrimaryKey($this->getPrimaryKey());

        $this->diContainer->setModule(StorageDependency::DATA_ADAPTER, $adapter);
    }
    
    protected function getPrimaryKey ()
    {
        return 'id';
    }
    
    abstract protected function getTableName() : string;
}