<?php


namespace Run\Processor;


use Verse\Run\Channel\AmqpReplyChannel;
use Verse\Run\ChannelMessage\ChannelMsg;
use Verse\Run\Processor\RunRequestProcessorProto;
use Verse\Run\RunRequest;

class TelegramProcessor extends RunRequestProcessorProto
{
    
    public function prepare()
    {
        // TODO: Implement prepare() method.
    }
    
    public function process(RunRequest $request)
    {
        $reply = new ChannelMsg();
        $reply->setChannelState($request->getChannelState());
        $reply->body = 'Reply to:'.$request->data['message']['text'];
        $reply->setDestination($request->getReply());
        
        $this->core->getDataChannel()->send($reply);
    }
}