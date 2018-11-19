<?php

namespace Base\Controller;

use Base\Auth\ChannelSecurityWrapperFactory;
use Base\Auth\SecurityWrapper\ChannelStateSecurityWrapperInterface;
use Base\Auth\StateProvider\RunRequestWrapperStateProvider;
use Base\Render\RendererInterface;
use Service\Balance\BalanceService;
use Service\User\UserService;
use Verse\Di\Env;
use Verse\Run\Controller\BaseControllerProto;

abstract class BasicController extends BaseControllerProto
{
    const STATE_KEY_USER_ID           = 'user_id';
    const STATE_KEY_BUDGET_ID         = 'budget_id';
    const STATE_AUTHORISE_DEFAULT_TTL = 2073600; // 24 days

    const DEFAULT_USER_ID = '';
    const DEFAULT_USER    = [];

    /**
     * @var RendererInterface
     */
    protected $_renderer;

    protected $_userId = self::DEFAULT_USER_ID;

    protected $_user = self::DEFAULT_USER;

    protected $_budgetId;

    protected $_budget;

    /**
     * @var ChannelStateSecurityWrapperInterface
     */
    protected $_secureState;

    protected function _pages()
    {
        if ($this->_userId) {
            return [
                'Главная' => '/',
                'Планы'   => '/suggestions',
                'Друзья'  => '/relations-users',
                'Бюджеты' => '/budgets',
            ];
        }

        return [
            'Главная' => '/',
        ];
    }

    public function run()
    {
        /// get renderer
        $this->_renderer = Env::getContainer()->bootstrap(RendererInterface::class);

        // get security state layer
        $securityFactory = Env::getContainer()->bootstrap(ChannelSecurityWrapperFactory::class);
        /* @var $securityFactory ChannelSecurityWrapperFactory */
        $stateProviderWrapper = new RunRequestWrapperStateProvider($this->requestWrapper);
        $this->_secureState = $securityFactory->getWrapper($stateProviderWrapper);

        // load user_id and state
        if ($this->requestWrapper->getState(self::STATE_KEY_USER_ID)) {
            $this->_userId = $this->_secureState->getState(self::STATE_KEY_USER_ID, self::DEFAULT_USER_ID);
        }

        if ($this->requestWrapper->getState(self::STATE_KEY_BUDGET_ID)) {
            $this->_budgetId = $this->_secureState->getState(self::STATE_KEY_BUDGET_ID);
        }

        $this->loadUser();

        // check user_id
        if (!method_exists($this, $this->method)) {
            $data = [
                    'url' => $this->requestWrapper->getResource(),
                ] + $this->_getRenderDefaultData();

            return $this->_renderer->render('404', $data,
                'page',
                [
                    __DIR__ . '/Template',
                ]
            );
        }

        return $this->{$this->method}();
    }

    public function validateMethod() : bool
    {
        return true;
    }

    protected function _getRenderDefaultData() : array
    {
        return [
            '_userId'      => $this->_userId,
            '_user'        => $this->_user,
            '_budgetId'    => $this->_budgetId,
            '_budget'      => $this->_budget,
            '_pages'       => $this->_pages(),
            '_currentPage' => $this->requestWrapper->getResource(),
        ];
    }

    public function loadUser()
    {
        if ($this->_userId) {
            $this->_user = (new UserService())->getUser($this->_userId);
            if ($this->_budgetId) {
                $this->_budget = (new BalanceService())->getBudget($this->_budgetId);
            }
        }
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