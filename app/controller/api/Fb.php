<?php defined('MAPLE') || exit('此檔案不允許讀取！');

use pimax\FbBotApp;
use pimax\Menu\MenuItem;
use pimax\Menu\LocalizedMenu;
use pimax\Messages\Message;
use pimax\Messages\MessageButton;
use pimax\Messages\StructuredMessage;
use pimax\Messages\MessageElement;
use pimax\Messages\MessageReceiptElement;
use pimax\Messages\Address;
use pimax\Messages\Summary;
use pimax\Messages\Adjustment;
use pimax\Messages\AccountLink;
use pimax\Messages\ImageMessage;
use pimax\Messages\QuickReply;
use pimax\Messages\QuickReplyButton;
use pimax\Messages\SenderAction;

class Fb extends ApiController {
    public function verify() {
        $gets = Input::get();

        $mode = $gets['hub_mode'];
        $token = $gets['hub_verify_token'];
        $challenge = $gets['hub_challenge'];

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === config('fb', 'verifyToken')) {
                Log::info('verify success !');
                echo $challenge;
            } else {
                Log::info('verify failed !'); 
            }
        }
    }

    public function webhook() {
        $posts = json_decode(file_get_contents('php://input'), true);

        if (!(isset($posts['object']) && $posts['object'] == 'page'))
            return false;

        $events = $posts['entry'];
        foreach ($events as $event) {
            foreach ($event['messaging'] as $msg) {
                $sender = $msg['sender']['id'];
                echo $msg['message'];
                Log::info('sender: ' . $sender);
                Log::info('msg: ' . $msg);
            }
        }
    }
}