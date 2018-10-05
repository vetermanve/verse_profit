<?php

namespace Base\Controller;

use Base\Render\RendererInterface;
use Verse\Di\Env;
use Verse\Run\Controller\SimpleController;

abstract class BasicController extends SimpleController
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
    
    protected function _render($template, $data = []) {
        return $this->_renderer->render($template, $data,
            'page',
            [
                $this->getClassDirectory().'/../Template',
            ]
        );
    }
    
    abstract protected function getClassDirectory();
}