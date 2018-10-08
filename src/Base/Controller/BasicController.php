<?php

namespace Base\Controller;

use Base\Render\RendererInterface;
use Stats\Events;
use Verse\Di\Env;
use Verse\Run\Controller\BaseControllerProto;
use Verse\Run\Util\Uuid;
use Verse\Statistic\WriteClient\StatsClientInterface;

abstract class BasicController extends BaseControllerProto
{
    /**
     * @var RendererInterface
     */
    protected $_renderer;

    protected $_userId = 0;

    protected $_scopeId;

    /**
     * @var StatsClientInterface
     */
    protected $_statsClient;

    /**
     * Landing constructor.
     */
    public function __construct()
    {
        $this->_renderer = Env::getContainer()->bootstrap(RendererInterface::class);
        $this->_statsClient = Env::getContainer()->bootstrap(StatsClientInterface::class);
    }
    
    protected function _pages() {
        return [
            'Home' => '/',
            // 'Test Actions' => '/stats-actions',
            'Current Scope Stats' => '/stats/view',
            'All Scopes Stats' => '/stats/site',
            'Contacts' => '/landing/contacts',
        ];
    }

    public function run()
    {
        if (!$this->_userId = $this->requestWrapper->getState('user_id')) {
            $this->_userId = Uuid::v4();
            $this->requestWrapper->setState('user_id', $this->_userId, 3600 * 24 * 365);
        }

        $this->_scopeId = $this->requestWrapper->getState('stats_id');
        if (!$this->_scopeId) {
            $this->_scopeId = Uuid::v4();
            $this->requestWrapper->setState('stats_id', $this->_scopeId, 3600 * 24 * 30);
        }

        if (!method_exists($this, $this->method)) {
            $data = [
                'url' => $this->requestWrapper->getResource(),
            ] + $this->_getRenderDefaultData();
            
            return $this->_renderer->render('404',  $data,
                'page',
                [
                    __DIR__ . '/Template',
                ]
            );
        }

        $this->_statsClient->event(
            Events::PAGE_VISIT, 
            $this->_userId, 
            $this->_scopeId, 
            [
                'page' => $this->requestWrapper->getResource(),
            ]
        );

        return $this->{$this->method}();
    }

    public function validateMethod() : bool
    {
        return true;
    }
    
    protected function _getRenderDefaultData() : array {
        return [
            '_userId' => $this->_userId,
            '_pages' => $this->_pages(),
            '_currentPage' => $this->requestWrapper->getResource(),
        ];
    }

    protected function _render($template, $data = [])
    {
        $data += $this->_getRenderDefaultData();

        return $this->_renderer->render($template, $data,
            'page',
            [
                $this->getClassDirectory() . '/../Template',
                __DIR__ . '/Template',
            ]
        );
    }

    abstract protected function getClassDirectory();
}