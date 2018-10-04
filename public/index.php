<?php

require_once __DIR__.'/../vendor/autoload.php';

use Base\Render\RenderSetupComponent;
use Verse\Run\Processor\SimplePageProcessor;
use Verse\Run\RunContext;
use Verse\Run\RunCore;
use Verse\Run\RuntimeLog;
use Verse\Run\Schema\RegularHttpRequestSchema;
use Verse\Run\Util\HttpEnvBuilder;

$env = HttpEnvBuilder::buildContext();

$schema = new RegularHttpRequestSchema();
$schema->setProcessor(new SimplePageProcessor());
$schema->setHttpEnv($env);
$schema->addComponent(new RenderSetupComponent());

$context = new RunContext();
$context->fill([
    RunContext::HOST     => $_SERVER['HTTP_HOST'],
    RunContext::IDENTITY => ('http.'.getmypid() . '@' . gethostname()),
    RunContext::IS_SECURE_CONNECTION => stripos($_SERVER['SERVER_PROTOCOL'],'https') === true
]);


$context->set(RunContext::GLOBAL_CONFIG, []);

$runtime = new RuntimeLog($context->get(RunContext::IDENTITY));
$runtime->catchErrors();

$core = new RunCore();
$core->setContext($context);
$core->setSchema($schema);
$core->setRuntime($runtime);

$core->configure();
$core->prepare();
$core->run();