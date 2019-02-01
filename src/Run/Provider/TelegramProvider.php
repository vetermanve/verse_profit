<?php


namespace Run\Provider;


use Verse\Run\Provider\RequestProviderProto;
use Verse\Run\RunRequest;
use Verse\Run\Util\ChannelState;

class TelegramProvider extends RequestProviderProto
{
    private $_accessKey = '';
    
    private $telegramHost = 'https://api.telegram.org';
    
    private $shouldProcess = true;
    
    private $localOffset = 0;
    
    
    public function prepare()
    {
        $this->_accessKey = $this->context->getScope('env','TELEGRAM_APP_KEY');
    }
    
    public function run()
    {
        $tick = 1;
                
        while ($this->shouldProcess) {
            $params = [
                'limit' => 5,
                'offset' => $this->getOffset(),
                'timeout' => $tick,
                'allowed_updates' => ['message'],
            ];
    
            $start = microtime(1);
            $updates = $this->_call('getUpdates', $params, $this->telegramHost);
            if ($updates !== null) {
                foreach ($updates as $update) {
                    $this->_processUpdate($update);
                }
            }
            
            $toSleep = microtime(1) - $start - $tick;
            if ($toSleep > 0) {
                usleep($toSleep * 1000); 
            }
        }
    }
    
    private function _processUpdate($update) {
        $request = new RunRequest($update['update_id'], '/', $update['message']['chat']['id']);
        $request->data = $update;
        
        $request->getChannelState()->set('telegram_user_id', $update['message']['from']['id']);
        
        $this->core->process($request);
        $this->storeOffest($update['update_id']);
    }
    
    private function storeOffest ($offset) 
    {
        $this->localOffset = $offset;
    }
    
    private function getOffset () 
    {
        return $this->localOffset + 1;
    }
    
    private function _call($method, $params, $host) {
        $website = $host.'/bot' . $this->_accessKey;
        $ch     = curl_init($website . '/'.$method);
        
//        $this->_debug('Api request '.$method, [
//            'api' => $website,
//            'params' => $params,
//        ]);
        
        curl_setopt_array($ch, [
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => \http_build_query($params),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 10,
        ]);
        
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result === false) {
            return null;
        }
        
        $data = json_decode($result, true);
        if (!isset($data['ok']) || $data['ok'] !== true) {
            return null;
        }
        
//        $this->_debug('Api response', $data ?: [
//            'string' => $result,
//        ]);
        
        return $data['result'];
    }
}