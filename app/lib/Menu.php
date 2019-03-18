<?php 

Load::lib('OALine/Message.php');

use \OA\Line\Message as Msg;

class Menu {
  public static function orderInfo() {
    return (
      Msg::flex()->altText('訂單資料')->template(
        Msg\FlexTemplate::bubble([
          'body' => Msg\FlexBox::create([
            Msg\FlexText::create('Tripresso 旅遊咖')->setWeight('bold')->setColor('#47b0f5')->setSize('xs'),
            Msg\FlexText::create('訂單 - A123456343')->setWeight('bold')->setMargin('lg')->setSize('xl'),
            Msg\FlexText::create('成立日期：2019-03-12')->setWeight('bold')->setMargin('lg')->setSize('xs')->setColor('#aaaaaa')->setWrap(true),
            Msg\FlexSeparator::create()->setMargin('md'),
            Msg\FlexText::create('保證出團 【懷舊雙鐵道旅行】大井川鐵道、隅田川遊船、富士登山列車、卡斯柏麗莎小鎮５日(東京進靜岡出)')->setWeight('bold')->setMargin('lg')->setSize('xs')->setColor('#ef9256')->setWrap(true),
          
            Msg\FlexBox::create([
              Msg\FlexBox::create([
                Msg\FlexText::create('行程編號')->setSize('sm')->setColor('#555555')->setFlex(0),
                Msg\FlexText::create('SPK05190409A')->setSize('sm')->setColor('#111111')->setAlign('end'),
              ])->setLayout('horizontal'),

              Msg\FlexBox::create([
                Msg\FlexText::create('團體編號')->setSize('sm')->setColor('#555555')->setFlex(0),
                Msg\FlexText::create('SPK05190409A')->setSize('sm')->setColor('#111111')->setAlign('end'),
              ])->setLayout('horizontal'),

              Msg\FlexBox::create([
                Msg\FlexText::create('出團日期')->setSize('sm')->setColor('#555555')->setFlex(0),
                Msg\FlexText::create('2019-05-20')->setSize('sm')->setColor('#111111')->setAlign('end'),
              ])->setLayout('horizontal'),

              Msg\FlexBox::create([
                Msg\FlexText::create('訂單進度')->setSize('sm')->setColor('#555555')->setFlex(0),
                Msg\FlexText::create('已付訂金')->setSize('sm')->setColor('#111111')->setAlign('end'),
              ])->setLayout('horizontal'),

              Msg\FlexSeparator::create()->setMargin('xxl'),

              Msg\FlexBox::create([
                Msg\FlexText::create('去程航班(04月08日)')->setSize('sm')->setColor('#555555')->setFlex(0),
                Msg\FlexText::create('中華航空CI108')->setSize('sm')->setColor('#111111')->setAlign('end'),
              ])->setLayout('horizontal')->setMargin('xxl'),

              Msg\FlexBox::create([
                Msg\FlexText::create('回程航班(04月12日)')->setSize('sm')->setColor('#555555')->setFlex(0),
                Msg\FlexText::create('中華航空CI169')->setSize('sm')->setColor('#111111')->setAlign('end'),
              ])->setLayout('horizontal'),

              Msg\FlexSeparator::create()->setMargin('xxl'),

              Msg\FlexBox::create([
                Msg\FlexText::create('總金額')->setSize('sm')->setColor('#555555')->setFlex(0),
                Msg\FlexText::create('$87,400')->setSize('sm')->setColor('#111111')->setAlign('end'),
              ])->setLayout('horizontal')->setMargin('xxl'),

              Msg\FlexBox::create([
                Msg\FlexText::create('未付金額')->setSize('sm')->setColor('#555555')->setFlex(0),
                Msg\FlexText::create('$0')->setSize('sm')->setColor('#111111')->setAlign('end'),
              ])->setLayout('horizontal'),

            ])->setLayout('vertical')->setMargin('xxl')->setSpacing('sm'),

            Msg\FlexSeparator::create()->setMargin('xxl'),

            Msg\FlexBox::create([
              Msg\FlexButton::create('primary')->setColor('#d6d6d6')->setAction(Msg\FlexAction::postback('< 回前頁', [], '已點擊 < 回前頁')),
              Msg\FlexButton::create('primary')->setColor('#e45a5a')->setMargin('xxl')->setAction(Msg\FlexAction::postback('行程內容 >', [], '已點擊 行程內容 >')),
            ])->setLayout('horizontal')->setMargin('md')
          ])->setLayout('vertical')
        ])
      )
    );
  }

  public static function tour() {
    return (
      Msg::flex()->altText('訂單資料')->template(
        Msg\FlexTemplate::bubble([
          'hero' => Msg\FlexImage::create('https://de0s2vtm6rzpn.cloudfront.net/4814/width/3_x128.jpg')->setSize('full')->setAspectRatio('20:13')->setAspectMode('cover')->setAction(Msg\FlexAction::uri('hehe', 'http://google.com')),
          'body' => Msg\FlexBox::create([
            Msg\FlexText::create('「魅力歐洲」五星小東歐精選12日～最美咖啡館、童話城堡、多瑙河遊船、集中營、地下鹽礦城之旅[含稅]')->setWeight('bold')->setSize('md')->setWrap(true),
            Msg\FlexBox::create([
              Msg\FlexIcon::create('https://scdn.line-apps.com/n/channel_devcenter/img/fx/review_gold_star_28.png')->setSize('sm'),
              Msg\FlexIcon::create('https://scdn.line-apps.com/n/channel_devcenter/img/fx/review_gold_star_28.png')->setSize('sm'),
              Msg\FlexIcon::create('https://scdn.line-apps.com/n/channel_devcenter/img/fx/review_gold_star_28.png')->setSize('sm'),
              Msg\FlexIcon::create('https://scdn.line-apps.com/n/channel_devcenter/img/fx/review_gold_star_28.png')->setSize('sm'),
              Msg\FlexIcon::create('https://scdn.line-apps.com/n/channel_devcenter/img/fx/review_gray_star_28.png')->setSize('sm'),
              Msg\FlexText::create('4.0')->setSize('sm')->setColor('#999999')->setMargin('md')->setFlex(0),
              Msg\FlexText::create('喜鴻旅遊')->setSize('xxs')->setColor('#999999')->setMargin('md')->setFlex(0),
            ])->setLayout('baseline')->setMargin('md'),

            Msg\FlexBox::create([
              Msg\FlexBox::create([
                Msg\FlexText::create('地點')->setColor('#aaaaaa')->setSize('sm')->setFlex(1),
                Msg\FlexText::create('捷克、匈牙利、斯洛伐克、波蘭、奧地利')->setColor('#666666')->setSize('sm')->setFlex(5)->setWrap(true),
              ])->setLayout('baseline')->setSpacing('sm'),

              Msg\FlexBox::create([
                Msg\FlexText::create('出發')->setColor('#aaaaaa')->setSize('sm')->setFlex(1),
                Msg\FlexText::create('04月06日 (週六)')->setColor('#ea867e')->setSize('sm')->setFlex(5)->setWrap(true),
              ])->setLayout('baseline')->setSpacing('sm'),

              Msg\FlexBox::create([
                Msg\FlexText::create('天數')->setColor('#aaaaaa')->setSize('sm')->setFlex(1),
                Msg\FlexText::create('7天')->setColor('#666666')->setSize('sm')->setFlex(5)->setWrap(true),
              ])->setLayout('baseline')->setSpacing('sm'),

            ])->setLayout('vertical')->setMargin('lg')->setSpacing('sm')
          ])->setLayout('vertical'),
          'footer' => Msg\FlexBox::create([
            Msg\FlexButton::create('primary')->setHeight('sm')->setColor('#36b7e8')->setAction(Msg\FlexAction::uri('更多', 'http://google.com')),
            Msg\FlexSpacer::create('sm')
          ])->setLayout('vertical')->setSpacing('sm'),
      ]))
    );
  }
}