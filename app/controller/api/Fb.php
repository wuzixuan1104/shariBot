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
                    case 'image':
                        $bot->send(new ImageMessage($message['sender']['id'], 'http://bit.ly/2p9WZBi'));
                        break;
                    case 'profile':
                        $user = $bot->userProfile($message['sender']['id']);
                        $bot->send(new StructuredMessage($message['sender']['id'],
                            StructuredMessage::TYPE_GENERIC,
                            [
                                'elements' => [
                                    new MessageElement($user->getFirstName()." ".$user->getLastName(), " ", $user->getPicture())
                                ]
                            ],
                            [ 
                                new QuickReplyButton(QuickReplyButton::TYPE_TEXT, 'QR button','PAYLOAD') 
                            ]
                        ));
                        break;

                    case 'button':
                        $bot->send(new StructuredMessage($message['sender']['id'],
                            StructuredMessage::TYPE_BUTTON,
                            [
                                'text' => 'Choose category',
                                'buttons' => [
                                    new MessageButton(MessageButton::TYPE_POSTBACK, 'First button', 'PAYLOAD 1'),
                                    new MessageButton(MessageButton::TYPE_POSTBACK, 'Second button', 'PAYLOAD 2'),
                                    new MessageButton(MessageButton::TYPE_POSTBACK, 'Third button', 'PAYLOAD 3')
                                ]
                            ],
                            [ 
                                new QuickReplyButton(QuickReplyButton::TYPE_TEXT, 'QR button','PAYLOAD') 
                            ]
                        ));
                        break;

                    case 'quick reply':
                        $bot->send(new QuickReply($message['sender']['id'], 'Your ad here!', 
                                [
                                    new QuickReplyButton(QuickReplyButton::TYPE_TEXT, 'QR button 1', 'PAYLOAD 1'),
                                    new QuickReplyButton(QuickReplyButton::TYPE_TEXT, 'QR button 2', 'PAYLOAD 2'),
                                    new QuickReplyButton(QuickReplyButton::TYPE_TEXT, 'QR button 3', 'PAYLOAD 3'),
                                ]
                        ));
                        break;

                    case 'location':
                        $bot->send(new QuickReply($message['sender']['id'], 'Please share your location', 
                                [
                                    new QuickReplyButton(QuickReplyButton::TYPE_LOCATION),
                                ]
                        ));
                        break;

                    case 'generic':
                        $bot->send(new StructuredMessage($message['sender']['id'],
                            StructuredMessage::TYPE_GENERIC,
                            [
                                'elements' => [
                                    new MessageElement("First item", "Item description", "", [
                                        new MessageButton(MessageButton::TYPE_POSTBACK, 'First button'),
                                        new MessageButton(MessageButton::TYPE_WEB, 'Web link', 'http://facebook.com')
                                    ]),
                                    new MessageElement("Second item", "Item description", "", [
                                        new MessageButton(MessageButton::TYPE_POSTBACK, 'First button'),
                                        new MessageButton(MessageButton::TYPE_POSTBACK, 'Second button')
                                    ]),
                                    new MessageElement("Third item", "Item description", "", [
                                        new MessageButton(MessageButton::TYPE_POSTBACK, 'First button'),
                                        new MessageButton(MessageButton::TYPE_POSTBACK, 'Second button')
                                    ])
                                ]
                            ],
                            [ 
                                new QuickReplyButton(QuickReplyButton::TYPE_TEXT, 'QR button','PAYLOAD')
                            ]
                        ));
                        break;
                    case 'list':
                        $bot->send(new StructuredMessage($message['sender']['id'],
                            StructuredMessage::TYPE_LIST,
                            [
                                'elements' => [
                                    new MessageElement(
                                        'Classic T-Shirt Collection', // title
                                        'See all our colors', // subtitle
                                        'http://bit.ly/2pYCuIB', // image_url
                                        [ // buttons
                                            new MessageButton(MessageButton::TYPE_POSTBACK, // type
                                                'View', // title
                                                'POSTBACK' // postback value
                                            )
                                        ]
                                    ),
                                    new MessageElement(
                                        'Classic White T-Shirt', // title
                                        '100% Cotton, 200% Comfortable', // subtitle
                                        'http://bit.ly/2pb1hqh', // image_url
                                        [ // buttons
                                            new MessageButton(MessageButton::TYPE_WEB, // type
                                                'View', // title
                                                'https://google.com' // url
                                            )
                                        ]
                                    )
                                ],
                                'buttons' => [
                                    new MessageButton(MessageButton::TYPE_POSTBACK, 'First button', 'PAYLOAD 1')
                                ]
                            ],
                            [
                                new QuickReplyButton(QuickReplyButton::TYPE_TEXT, 'QR button','PAYLOAD')
                            ]
                        ));
                        break;

                    case 'receipt':
                        $bot->send(new StructuredMessage($message['sender']['id'],
                            StructuredMessage::TYPE_RECEIPT,
                            [
                                'recipient_name' => 'Fox Brown',
                                'order_number' => rand(10000, 99999),
                                'currency' => 'USD',
                                'payment_method' => 'VISA',
                                'order_url' => 'http://facebook.com',
                                'timestamp' => time(),
                                'elements' => [
                                    new MessageReceiptElement("First item", "Item description", "", 1, 300, "USD"),
                                    new MessageReceiptElement("Second item", "Item description", "", 2, 200, "USD"),
                                    new MessageReceiptElement("Third item", "Item description", "", 3, 1800, "USD"),
                                ],
                                'address' => new Address([
                                    'country' => 'US',
                                    'state' => 'CA',
                                    'postal_code' => 94025,
                                    'city' => 'Menlo Park',
                                    'street_1' => '1 Hacker Way',
                                    'street_2' => ''
                                ]),
                                'summary' => new Summary([
                                    'subtotal' => 2300,
                                    'shipping_cost' => 150,
                                    'total_tax' => 50,
                                    'total_cost' => 2500,
                                ]),
                                'adjustments' => [
                                    new Adjustment([
                                        'name' => 'New Customer Discount',
                                        'amount' => 20
                                    ]),
                                    new Adjustment([
                                        'name' => '$10 Off Coupon',
                                        'amount' => 10
                                    ])
                                ]
                            ]
                        ));
                        break;

                    case 'set menu':
                        $bot->deletePersistentMenu();
                        $bot->setPersistentMenu([
                            new LocalizedMenu('default', false, [
                                new MenuItem(MenuItem::TYPE_NESTED, 'My Account', [
                                    new MenuItem(MenuItem::TYPE_NESTED, 'History', [
                                        new MenuItem(MenuItem::TYPE_POSTBACK, 'History Old', 'HISTORY_OLD_PAYLOAD'),
                                        new MenuItem(MenuItem::TYPE_POSTBACK, 'History New', 'HISTORY_NEW_PAYLOAD')
                                    ]),
                                    new MenuItem(MenuItem::TYPE_POSTBACK, 'Contact Info', 'CONTACT_INFO_PAYLOAD')
                                ])
                            ])
                        ]);
                        break;

                    case 'delete menu':
                        $bot->deletePersistentMenu();
                        break;

                    case 'login':
                        $bot->send(new StructuredMessage($message['sender']['id'],
                            StructuredMessage::TYPE_GENERIC,
                            [
                                'elements' => [
                                    new AccountLink(
                                        'Welcome to Bank',
                                        'To be sure, everything is safe, you have to login to your administration.',
                                        'https://www.example.com/oauth/authorize',
                                        'https://www.facebook.com/images/fb_icon_325x325.png')
                                ]
                            ]
                        ));
                        break;


                    case 'logout':
                        $bot->send(new StructuredMessage($message['sender']['id'],
                            StructuredMessage::TYPE_GENERIC,
                            [
                                'elements' => [
                                    new AccountLink(
                                        'Welcome to Bank',
                                        'To be sure, everything is safe, you have to login to your administration.',
                                        '',
                                        'https://www.facebook.com/images/fb_icon_325x325.png',
                                        TRUE)
                                ]
                            ]
                        ));
                        break;
                    case 'sender action on':
                        $bot->send(new SenderAction($message['sender']['id'], SenderAction::ACTION_TYPING_ON));
                        break;

                    // When bot receive "sender action off"
                    case 'sender action off':
                        $bot->send(new SenderAction($message['sender']['id'], SenderAction::ACTION_TYPING_OFF));
                        break;
                    // When bot receive "set get started button"
                    case 'set get started button':
                        $bot->setGetStartedButton('PAYLOAD - get started button');
                        break;
                    // When bot receive "delete get started button"
                    case 'delete get started button':
                        $bot->deleteGetStartedButton();
                        break;
                    // When bot receive "show greeting text"
                    case 'show greeting text':
                        $response = $bot->getGreetingText();
                        $text = "";
                        if(isset($response['data'][0]['greeting']) AND is_array($response['data'][0]['greeting'])){
                            foreach ($response['data'][0]['greeting'] as $greeting)
                            {
                                $text .= $greeting['locale']. ": ".$greeting['text']."\n";
                            }
                        } else {
                            $text = "Greeting text not set!";
                        }
                        $bot->send(new Message($message['sender']['id'], $text));
                        break;
                    // When bot receive "delete greeting text"
                    case 'delete greeting text':
                        $bot->deleteGreetingText();
                        break;
                    // When bot receive "set greeting text"
                    case 'set greeting text':
                        $bot->setGreetingText([
                            [
                                "locale" => "default",
                                "text" => "Hello {{user_full_name}}"
                            ],
                            [
                                "locale" => "en_US",
                                "text" => "Hi {{user_first_name}}, welcome to this bot."
                            ],
                            [
                                "locale" => "de_DE",
                                "text" => "Hallo {{user_first_name}}, herzlich willkommen."
                            ]
                        ]);
                        break;
                    // When bot receive "set target audience"
                    case 'show target audience':
                        $response = $bot->getTargetAudience();
                        break;
                    // When bot receive "set target audience"
                    case 'set target audience':
                        $bot->setTargetAudience("all");
                        //$bot->setTargetAudience("none");
                        //$bot->setTargetAudience("custom", "whitelist", ["US", "CA"]);
                        //$bot->setTargetAudience("custom", "blacklist", ["US", "CA"]);
                        break;
                    // When bot receive "delete target audience"
                    case 'delete target audience':
                        $bot->deleteTargetAudience();
                        break;
                    // When bot receive "show domain whitelist"
                    case 'show domain whitelist':
                        $response = $bot->getDomainWhitelist();
                        $text = "";
                        if(isset($response['data'][0]['whitelisted_domains']) AND is_array($response['data'][0]['whitelisted_domains'])){
                            foreach ($response['data'][0]['whitelisted_domains'] as $domains)
                            {
                                $text .= $domains."\n";
                            }
                        } else {
                            $text = "No domains in whitelist!";
                        }
                        $bot->send(new Message($message['sender']['id'], $text));
                        break;
                    // When bot receive "set domain whitelist"
                    case 'set domain whitelist':
                        //$bot->setDomainWhitelist("https://petersfancyapparel.com");
                        $bot->setDomainWhitelist([
                            "https://petersfancyapparel-1.com",
                            "https://petersfancyapparel-2.com",
                        ]);
                        break;
                    // When bot receive "delete domain whitelist"
                    case 'delete domain whitelist':
                        $bot->deleteDomainWhitelist();
                        break;

                    default:
                        if (!empty($command)) // otherwise "empty message" wont be understood either
                            $bot->send(new Message($message['sender']['id'], 'Sorry. I don’t understand you.'));

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