<?php
require_once "TP/class.TemplatePower.inc.php";
require_once "class/model/order/payment/allpay.php"; 
require_once "conf/creditcard.php";
if($_POST){
    $card = new Model_Order_Payment_Allpay($cms_cfg['creditcard'], $cms_cfg['Hash'],$cms_cfg['exe_mode']);
    $card->checkout($_POST['orderid'], $_POST['price']);
}

