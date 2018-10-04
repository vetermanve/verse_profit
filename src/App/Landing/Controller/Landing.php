<?php

namespace App\Landing\Controller;

use Base\Render\RendererInterface;
use Verse\Di\Env;
use Verse\Run\Controller\SimpleController;
use Verse\Run\Util\Uuid;

class Landing extends SimpleController
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

    public function index () 
    {
        $statsId = $this->requestWrapper->getState('stats_id');
        if (!$statsId) {
            $statsId = Uuid::v4();
            $this->requestWrapper->setState('stats_id', $statsId, 7200);
        }
        
        return $this->_render(__FUNCTION__, [
            'title' => 'Main Page',
            'statsId' => $statsId,
        ]);
    }
    
    public function contacts () 
    {
        return $this->_render(__FUNCTION__, [
            'title' => 'Contacts Page',
        ]);
    }
    
    public function regenerate () 
    {
        $statsId = Uuid::v4();
        $this->requestWrapper->setState('stats_id', $statsId);
        
        return $this->_render(__FUNCTION__, [
            'title' => 'Regenerated!',
            'statsId' => $statsId,
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