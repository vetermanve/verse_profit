<?php


namespace App\Suggestions\Controller;


use Base\Controller\BasicController;
use Service\Suggestion\SuggestionService;

class Suggestions extends BasicController
{
    protected $message = '';

    public function index()
    {
        $budgetId = $this->_budgetId;
        $fromDate = $this->p('from', strtotime('-5day'));
        $toDate = $this->p('to', strtotime('+20day'));

        $service = new SuggestionService();
        $suggestions = $service->getSuggestions($budgetId, $fromDate, $toDate);

        return $this->_render(__FUNCTION__, [
            'suggestions' => $suggestions,
            'message' => $this->message,
        ]);
    }

    public function add()
    {
        $budgetId = $this->_budgetId;
        
        $name = $this->p('name');
        $dateRaw = $this->p('date');
        $date = strtotime($dateRaw) ?? time();
        $amount = $this->p('amount');
        
        $service = new SuggestionService();
        $suggestion = $service->addSuggestion($budgetId, $name, $amount, $date);
        
        if ($suggestion) {
            $this->message = 'План сохранен! Дата: '.date('d.m.Y');
        }

        return $this->index();
    }

    protected function getClassDirectory()
    {
        return __DIR__;
    }
}