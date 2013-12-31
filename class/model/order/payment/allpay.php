<?php
require_once "returncode/allpay.php";
require_once "class/mcrypt/aes.php";
class Model_Order_Payment_Allpay {
    //put your code here
    protected $config;
    protected $hash = array();
    protected $mode;
    protected $fields = array();
    protected $codedata = array();
    protected $url = array(
        'testing' => "http://pay-stage.allpay.com.tw/payment/gateway",
        'running' => "https://pay.allpay.com.tw/payment/gateway",
    );
    protected $template = "templates/ws-cart-card-transmit-tpl.html";
    protected $xml_template = "templates/allpay-xmldata.xml";
    function __construct($config,$hash,$mode="testing") {
        $this->config = $config;
        $this->mode = $mode;
        $this->hash = $hash;
        $this->fields['MerchantID'] = $this->config['MerchantID'];
        $this->fields['PaymentType'] = $this->config['PaymentType'];
        $this->codedata['MerchantID'] = $this->config['MerchantID'];
    }
    //結帳
    function checkout($o_id,$total_price,$extra_info=array()){
        $this->codedata['MerchantTradeNo'] = $o_id;
        $this->codedata['MerchantTradeDate'] = date("Y/m/d H:i:s");
        $this->codedata['TotalAmount'] = $total_price;
        $this->codedata = array_merge($this->codedata,$this->config['params']);
        if(!empty($extra_info)){
            foreach($extra_info as $k => $v){
                if(!isset($this->codedata[$k])){
                    $this->codedata[$k] = $v;
                }
            }
        }
        $this->fields['XMLData'] = $this->make_xml();
        $tpl = new TemplatePower($this->template);
        $tpl->prepare();
        $tpl->assignGlobal("AUTHORIZED_URL",$this->url[$this->mode]);
        foreach($this->fields as $k => $v){
            $tpl->newBlock("CARD_FIELD_LIST");
            $tpl->assign(array(
                "TAG_KEY" => $k,
                "TAG_VALUE" => $v
            ));
        }
        $tpl->printToScreen();
        die();
    }
    //更新訂單
    function update_order(DB $db,SimpleXMLElement $result){
        global $main;
        $oid = $result->Data->MerchantTradeNo;
        if($result->Data->RtnCode=='1'){ //交易成功
            $sql = "update ".$db->prefix("order")." set "
                    . "o_status='1', "
                    . "TradeNo = '".$result->Data->TradeNo."', "
                    . "RtnCode = '".$result->Data->RtnCode."', "
                    . "gwsr = '".$result->Data->gwsr."', "
                    . "process_date = '".$result->Data->process_date."', "
                    . "auth_code = '".$result->Data->auth_code."' "
                    . "where o_id='".$oid."'";
        }else{
            //更新訂單狀態
            if(!in_array($result->Data->RtnCode,'10100054','10100089')){ //錯誤原因非訂單編號重複
                $sql = "update ".$db->prefix("order")." set "
                        . "o_status='10', "
                        . "TradeNo = '".$result->Data->TradeNo."', "
                        . "RtnCode = '".$result->Data->RtnCode."', "
                        . "gwsr = '".$result->Data->gwsr."', "
                        . "process_date = '".$result->Data->process_date."', "
                        . "auth_code = '".$result->Data->auth_code."' "
                        . "where o_id='".$oid."'";
                $main->update_order_stock(10,$oid);
            }
        }
        $db->query($sql);
    }
    /*製作xml*/
    function make_xml(){
        $tpl = new TemplatePower("templates/allpay-xmldata.xml");
        $tpl->prepare();
        foreach($this->codedata as $k => $v){
            if(!in_array($k,array("MerchantTradeDate"))){
                $tpl->assignGlobal($k,urlencode($v));
            }else{
                $tpl->assignGlobal($k,$v);
            }
        }
        $XMLdata =  $tpl->getOutputContent();
        return Mcrypt_Aes::aes128cbcEncrypt($XMLdata , $this->hash['IV'] , $this->hash['Key']);
    }
    //解析授權回轉結果
    function parse_xmldata($postdata){
        if($postdata){
            $postdata = str_replace(" ", "+", $postdata);
            $xmldata = Mcrypt_Aes::aes128cbcDecrypt($postdata, $this->hash['IV'], $this->hash['Key']);
            $returnXML = new SimpleXMLElement($xmldata);        
        }
        return $returnXML;
    }
    
}
