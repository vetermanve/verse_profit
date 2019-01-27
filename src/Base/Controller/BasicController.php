<?php

namespace Base\Controller;

use Base\Auth\ChannelSecurityWrapperFactory;
use Base\Auth\SecurityWrapper\ChannelStateSecurityWrapperInterface;
use Base\Auth\StateProvider\RunRequestWrapperStateProvider;
use Base\Render\RendererInterface;
use Service\Budget\BudgetService;
use Service\User\UserService;
use Verse\Di\Env;
use Verse\Run\Controller\BaseControllerProto;

abstract class BasicController extends BaseControllerProto
{
    protected const STATE_KEY_USER_ID           = 'user_id';
    protected const STATE_KEY_BUDGET_ID         = 'budget_id';
    protected const STATE_AUTHORISE_DEFAULT_TTL = 2073600; // 24 days

    protected const DEFAULT_USER_ID = '';
    protected const DEFAULT_USER    = [];
    
    protected const DEFAULT_BUDGET_ID = '';
    protected const DEFAULT_BUDGET    = [];

    /**
     * @var RendererInterface
     */
    protected $_renderer;

    protected $_userId = self::DEFAULT_USER_ID;

    protected $_user = self::DEFAULT_USER;

    protected $_budgetId = self::DEFAULT_BUDGET_ID;

    protected $_budget = self::DEFAULT_BUDGET;

    /**
     * @var ChannelStateSecurityWrapperInterface
     */
    protected $_secureState;
    
    protected $message = ''; 

    protected function _pages()
    {
        if ($this->_userId) {
            return [
                // 'Главная'   => '/landing/',
                'Календарь' => '/calendar/',
                'Планы'     => '/plans/',
                'Счета'     => '/balances/',
                'Транзакции'=> '/balances-transactions/',
                'Друзья'    => '/relations-users/',
//                'Бюджеты'   => '/budgets/',
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
            return $this->_page404();
        }
        
        $this->prepare();
        try {
            return $this->{$this->method}();    
        } catch (\Throwable $exception) {
            return $this->_renderer->render('500', $this->_getRenderDefaultData() + [
                    'error' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                ],
                'page',
                [
                    __DIR__ . '/Template',
                ]
            );     
        }
    }
    
    public function prepare () 
    {
        
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
    
    protected function authoriseUser ($userId, $budgetId = null) 
    {
        $this->_userId = $userId;
        $this->_secureState->setState(self::STATE_KEY_USER_ID, $this->_userId, self::STATE_AUTHORISE_DEFAULT_TTL);
        
        if ($budgetId) {
            $this->_budgetId = $budgetId;
            $this->_secureState->setState(self::STATE_KEY_BUDGET_ID, $this->_budgetId, self::STATE_AUTHORISE_DEFAULT_TTL);
        }
        
        $this->loadUser();
    }
    
    protected function unAuthoriseUser () 
    {
        $this->_secureState->setState(self::STATE_KEY_USER_ID, null, 3600);
        $this->_secureState->setState(self::STATE_KEY_BUDGET_ID, null, 3600);
    
        $this->_userId   = self::DEFAULT_USER_ID;
        $this->_user     = self::DEFAULT_USER;
        
        $this->_budgetId = self::DEFAULT_BUDGET_ID;
        $this->_budget   = self::DEFAULT_BUDGET;
    }

    public function loadUser()
    {
        if ($this->_userId) {
            $this->_user = (new UserService())->getUser($this->_userId);
            if ($this->_budgetId) {
                $this->_budget = (new BudgetService())->getBudget($this->_budgetId);
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
    
    protected function _page404()
    {
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
}