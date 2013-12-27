<?php
$cms_cfg['creditcard']['MerchantID'] = "2000132";//特店代碼
$cms_cfg['creditcard']['PaymentType'] = "CREDIT";//信用卡固定CREDIT
//信用卡卡號，若要在AllPay顯示信用卡頁讓使用者輸入的話，請放0。 連同下面的CardValidMM、 CardValidYY及 CardCVV2也都請放0
$cms_cfg['creditcard']['params']['CardNo'] = 0;        
$cms_cfg['creditcard']['params']['CardValidMM'] = 0;   //信用卡有效月份
$cms_cfg['creditcard']['params']['CardValidYY'] = 0;   //信用卡有效年份
$cms_cfg['creditcard']['params']['CardCVV2'] = 0;      //信用卡背後末三碼，若不驗證末三碼，請放空值。
$cms_cfg['creditcard']['params']['UnionPay'] = 0;      //是否為銀聯卡。
$cms_cfg['creditcard']['params']['Installment'] = 0;   //分期期數，若不分期請帶0
$cms_cfg['creditcard']['params']['ThreeD'] = 0;        //是否使用3D驗證。使用-請帶1，不使用-請帶0。
$cms_cfg['creditcard']['params']['CharSet'] = 'utf-8'; //中文編碼格式
$cms_cfg['creditcard']['params']['Enn'] = '';          //英文交易時，請帶e，否則請放空值。
$cms_cfg['creditcard']['params']['BankOnly'] = '';     //限制交易的銀行卡別，若無限制交易的銀行卡別，請放空值。
$cms_cfg['creditcard']['params']['Redeem'] = '';       //請放空值。設為Y時，會進入紅利折抵的交易流程。(有申請紅利的商家，紅利折抵參數才會有作用。)
$cms_cfg['creditcard']['params']['ReturnURL'] = "http://localhost/payment_allpay/card-test3.php"; //接收授權結果通知網址(以client post方式返回)
$cms_cfg['creditcard']['params']['ServerReplyURL'] = "http://localhost/payment_allpay/card-test3.php"; //接收授權結果通知網址(以server post方式返回)
$cms_cfg['creditcard']['params']['ClientBackURL'] = ""; //授權結果頁給使用者點選後，導回商家的回傳網址。
$cms_cfg['Hash']['Key'] = "ejCk326UnaZWKisg";
$cms_cfg['Hash']['IV'] = "q9jcZX8Ib9LM8wYk";
?>