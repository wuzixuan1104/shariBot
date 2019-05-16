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
        Log::info($mode);
        Log::info($token);
        Log::info($challenge);
        
        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === config('fb', 'verifyToken')) {
      
                Log::info('WEBHOOK_VERIFIED');
                return $challenge;
            } else {
                return false;                  
            }
        }
    }

    public function webhook() {
        $posts = Input::post();
        if (isset($posts['page']) && $posts['page'])
            return 'EVENT_RECEIVED';
        return false;

        $verify_token = config('fb', 'verifyToken');
        $access_token = config('fb', 'accessToken');

        $hub_verify_token = null;
        Log::info($verify_token);
        Log::info($access_token);
        Log::info($_REQUEST);

        if(isset($_REQUEST['hub_challenge'])) {
             $challenge = $_REQUEST['hub_challenge'];
             $hub_verify_token = $_REQUEST['hub_verify_token'];
        }
        if ($hub_verify_token === $verify_token) {
            return $challenge;
        }
        return true;
        // $bot = new FbBotApp($token);

        // Log::info($_REQUEST);
        // // Receive something
        // if (!empty($_REQUEST['hub_mode']) && $_REQUEST['hub_mode'] == 'subscribe' && $_REQUEST['hub_verify_token'] == $verify_token) {
        //     // Webhook setup request
        //     echo $_REQUEST['hub_challenge'];
        // } else {
        //     // Other event
        //     $data = json_decode(file_get_contents("php://input"), true, 512, JSON_BIGINT_AS_STRING);
        //     if (!empty($data['entry'][0]['messaging'])) {
        //         foreach ($data['entry'][0]['messaging'] as $message) {
        //             // Skipping delivery messages
        //             if (!empty($message['delivery'])) {
        //                 continue;
        //             }
        //             // skip the echo of my own messages
        //             if (($message['message']['is_echo'] == "true")) {
        //                 continue;
        //             }
        //             $command = "";
        //             // When bot receive message from user
        //             if (!empty($message['message'])) {
        //                 $command = trim($message['message']['text']);
        //             // When bot receive button click from user
        //             } else if (!empty($message['postback'])) {
        //                 $text = "Postback received: ".trim($message['postback']['payload']);
        //                 $bot->send(new Message($message['sender']['id'], $text));
        //                 continue;
        //             }
        //             // Handle command
        //             switch ($command) {
        //                 // When bot receive "text"
        //                 case 'text':
        //                     Log::info('text');
        //                     Log::info($message['sender']['id']);
        //                     $bot->send(new Message($message['sender']['id'], 'This is a simple text message.'));
        //                     break;

        //                 default:
        //                     Log::info('default');
        //                     if (!empty($command)) // otherwise "empty message" wont be understood either
        //                         $bot->send(new Message($message['sender']['id'], 'Sorry. I don’t understand you.'));
        //             }
        //         }
        //     }
        // }
    }
}