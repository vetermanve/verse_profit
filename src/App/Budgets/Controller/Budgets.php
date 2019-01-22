<?php


namespace App\Budgets\Controller;


use Base\Controller\BasicController;
use Service\Budget\BudgetService;
use Service\Budget\Model\BudgetModel;
use Service\Budget\Model\BudgetOwnersModel;
use Service\Relations\Model\RelationModel;
use Service\Relations\RelationsService;
use Service\User\UserService;
use Verse\Run\Spec\HttpResponseSpec;

class Budgets extends BasicController
{
    /**
     * @return BudgetService
     */
    private function getService()
    {
        return new BudgetService();
    }
    
    public function index()
    {
        $budgets = $this->getService()->getBudgetsByUserId($this->_userId);
        
        return $this->_render(__FUNCTION__, [
            'budgets' => $budgets,
            'message' => $this->message,
        ]);
    }
    
    public function add()
    {
        $name        = $this->p('name');
        $desc        = $this->p('desc');
        $isSwitchNow = $this->p('switch');
        
        $budget = null;
        
        if ($name) {
            $budget = $this->getService()->createBudget($this->_userId, $name, $desc);
        }
        
        if ($budget) {
            $this->message = 'Бюджет создан!';
        } else {
            $this->message = 'Не удалось сохранить бюджет :(';
        }
        
        if ($isSwitchNow) {
            $this->selectBudget($budget[BudgetModel::ID]);
        }
        
        return $this->index();
    }
    
    public function show()
    {
        $budgetId = $this->p('id');
        
        $budget = $this->getService()->getBudget($budgetId);
        if (!$budget) {
            throw new \RuntimeException('Budget not found', HttpResponseSpec::HTTP_CODE_NOT_FOUND);
        }
        
        $budgetRelations = $this->getService()->getBudgetOwnersByBudgetId($budgetId);
        $ownersUserIds   = array_column($budgetRelations, BudgetOwnersModel::USER_ID);
        
        $userRelations  = (new RelationsService())->getRelations($this->_userId);
        $friendsUserIds = array_column($userRelations, RelationModel::RELATED_USER_ID);
        
        $userService  = new UserService();
        $ownersUsers  = $userService->getUsers($ownersUserIds);
        $friendsUsers = $userService->getUsers($friendsUserIds);
        
        return $this->_render(__FUNCTION__, [
            'budget'     => $budget,
            'ownerUsers' => $ownersUsers,
            'friends'    => $friendsUsers,
            'message'    => $this->message,
        ]);
    }
    
    public function addToBudget()
    {
        $id     = $this->p('id');
        $userId = $this->p('user_id');
        
        if ($id && $userId) {
            $res = $this->getService()->addUserToBudget($id, $userId, $this->_userId);
            if ($res) {
                $this->message = 'Пользователь добавлен в бюджет!';
            } else {
                $this->message = 'Пользователь не добавлен в бюджет :(';
            }
        }
        
        return $this->show();
    }
    
    public function removeFromBudget () 
    {
        $budgetId     = $this->p('id');
        $userId = $this->p('user_id');
    
        if ($budgetId && $userId) {
            
            $budget = $this->getService()->getBudget($budgetId);
            if (!$budget) {
                return $this->_page404();
            }
            
            if ($userId === $budget[BudgetModel::OWNER_ID]) {
                $this->message = 'Владельца нельзя удалить из бюджета!';
            } else {
                $res = $this->getService()->removeUserFromBudget($budgetId, $userId, $this->_userId);
                if ($res) {
                    $this->message = 'Пользователь удален из бюджета!';
                } else {
                    $this->message = 'Пользователь не удален из бюджета :(';
                }
            }
        }
        
        return $this->show();
    }
    
    public function select()
    {
        $budgetId = $this->p('budget_id');
        $this->selectBudget($budgetId);
        $this->message = 'Бюджет выбран!';
        
        return $this->index();
    }
    
    private function selectBudget($budgetId)
    {
        $this->_budgetId = $budgetId;
        $this->_secureState->setState(self::STATE_KEY_BUDGET_ID, $this->_budgetId, self::STATE_AUTHORISE_DEFAULT_TTL);
        $this->loadUser();
    }
    
    protected function getClassDirectory()
    {
        return __DIR__;
    }
}