<?php


namespace App\Stats\Controller;


use Base\Render\RendererInterface;
use Verse\Di\Env;
use Verse\Run\Controller\SimpleController;

class Stats extends SimpleController
{
    /**
     * @var RendererInterface
     */
    private $_renderer;

    /**
     * Landing constructor.
     */
    public function __construct()
    {
        $this->_renderer = Env::getContainer()->bootstrap(RendererInterface::class);
    }
    
    public function view () 
    {
        return $this->_render(__FUNCTION__, [
            
        ]);
    }
    
    public function actions ()
    {
        return $this->_render(__FUNCTION__, [

        ]);
    }

    private function _render($template, $data = []) {
        return $this->_renderer->render($template, $data,
            'page',
            [
                __DIR__.'/../Template',
            ]
        );
    }
}