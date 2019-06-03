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

Load::lib('Curl.php');

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
        $bot = new FbBotApp(config('fb', 'accessToken'));

        $posts = json_decode(file_get_contents('php://input'), true);
        Log::info(json_encode($posts));
        


        if (!empty($posts['entry'][0]['messaging'])) {
            foreach ($posts['entry'][0]['messaging'] as $message) {
                $command = "";
                if (!empty($message['message'])) {
                    Log::info('text: ' . $message['message']['text']);
                    $command = trim($message['message']['text']);

                } else if (!empty($message['postback'])) {
                    $text = "Postback received: ".trim($message['postback']['payload']);
                    $bot->send(new Message($message['sender']['id'], $text));
                    continue;
                }

                switch ($command) {
                    case 'text':
                        $bot->send(new Message($message['sender']['id'], 'This is a simple text message.'));
                        break;
                }
            }
        }

        // $events = $posts['entry'];
        // foreach ($events as $event) {
        //     foreach ($event['messaging'] as $msg) {
        //         $sender = $msg['sender']['id'];
                

        //         echo $msg['message']['text'];
        //         Log::info('sender: ' . $sender);
        //         Log::info('msg: ' . json_encode($msg));


        //     }
        // }
    }

}