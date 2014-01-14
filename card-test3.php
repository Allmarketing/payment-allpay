<?php
require_once "TP/class.TemplatePower.inc.php";
require_once "class/model/order/payment/allpay.php"; 
require_once "class/mcrypt/aes.php"; 
require_once "conf/config.inc.php";
require_once "conf/creditcard.php";
require_once "conf/database.php";
include_once("libs/libs-mysql.php");
$db = new DB($cms_cfg['db_host'],$cms_cfg['db_user'],$cms_cfg['db_password'],$cms_cfg['db_name'],$cms_cfg['tb_prefix']);
$tpl = new TemplatePower("test3.html");
$tpl->prepare();
/*初始化payment物件*/
$card = new Model_Order_Payment_Allpay($cms_cfg['creditcard']);
/*解析回傳結果*/
$returnXML = $card->parse_xmldata($_POST['XMLData']);
/*更新訂單*/
$sql = $card->update_order($db,$returnXML);
/*輸出回傳結果*/
$tpl->gotoBlock("_ROOT");
$tpl->assign("UPDATE_ORDER_SQL",$sql);
foreach($returnXML->Data->children() as $elm){
    $tpl->assign("MSG_".strtoupper($elm->getName()),$elm);
    if($elm->getName()=="RtnCode"){
        $idx = sprintf("%s",$elm);
        $tpl->assign("MSG_".strtoupper($elm->getName())."_STR",  Model_Order_Payment_Returncode_Allpay::$code[$idx]);
    }
}
$tpl->printToScreen();


