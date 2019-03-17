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
}