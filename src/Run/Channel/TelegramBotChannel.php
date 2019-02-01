<?php


namespace Run\Channel;


use Verse\Run\Channel\DataChannelProto;
use Verse\Run\ChannelMessage\ChannelMsg;

class TelegramBotChannel extends DataChannelProto
{
    
    private $_accessKey = '';
    
    private $telegramHost = 'https://api.telegram.org';
    
    /**
     * Подготовка к отправке данных
     *
     * @return mixed
     */
    public function prepare()
    {
        $this->_accessKey = $this->context->getScope('env', 'TELEGRAM_APP_KEY');
    }
    
    /**
     * Непосредственно отпрвка данных
     *
     * @param $msg
     *
     * @return null
     */
    public function send(ChannelMsg $msg)
    {
//        $state = $msg->getChannelState()
        $params = [
            'chat_id' => $msg->getDestination(),
            'text' => $msg->getBody(),
            'parse_mode' => 'Markdown',
            'disable_web_page_preview' => false,
        ];
        
        $method = 'sendMessage';
        $website = $this->telegramHost.'/bot' . $this->_accessKey;
        $ch     = curl_init($website . '/'.$method);

//        $this->_debug('Api request '.$method, [
//            'api' => $website,
//            'params' => $params,
//        ]);
    
        curl_setopt_array($ch, [
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $params,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 10,
        ]);
    
        $result = curl_exec($ch);
        curl_close($ch);
    
        $data = json_decode($result, true);
    }
}