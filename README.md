payment-allpay
============

歐付寶信用卡串接


前置設定
---------------
1.設定conf/creditcard.php裡的 $cms_cfg['creditcard']['MerchantID'] (特店編號)、 $cms_cfg['creditcard']['params']各項目及$cms_cfg['Hash']各項目。<br/>
2.Model_Order_Payment_Returncode_Allpay裡的項目會持續新增，請不定時至廠商後台->系統開發管理->交易狀態代碼查詢。
3.$cms_cfg['Hash']的Key及IV在廠商後台->基本資料查詢->廠商基本資料查詢，請依序填入[一般金流介接HashKey]及[一般金流介接HashIV]
4.歐付寶信用卡串接會使用AES加密，請確認class/mcrypt/aes.php存在，才可以正確執行加、解密的操作。


測試流程
---------------
1.執行card-test1.php，輸入訂單號碼及訂單價格.<br/>
2.前述訂單號碼及訂單價格由card-test2.php接收後，依documents/信用卡介接規格說明.pdf第四頁開始的說明，以post方式傳給歐付寶伺服器.<br/>
3.通過驗證就會進入線上刷頁頁面，若沒通過則直接導回結果頁.<br/>
4.結果頁，輸出回傳的資訊，及產品修改訂單的sql.


api說明
---------------

### Model_Order_Payment_Allpay::__construct($config,$hahs,$mode="testing")

    1.$config: 即conf/credictcard.php裡的$cms_cfg['creditcard'].
    2.$hash: 即conf/credictcard.php裡的$cms_cfg['Hash'].
    3.$mode: 預設是[testing]，代表測試模式，正式環境需改為running，此設定會決定傳送到哪一個歐付寶的主機，即Model_Order_Payment_Allpay::$url裡的項目.


### Model_Order_Payment_Allpay::checkout($o_id,$total_price,$extra_info=array())

    1.$o_id:訂單號碼.
    2.$total_price:訂單價格.
    3.$extra_info:額外的欄位，預設是空陣列，也就是不加新欄位，如果要加新欄位，請以關聯式陣列輸入，例如: array('email'=>'xxxx@some.domain','tel'=>'88881888').

### Model_Order_Payment_Allpay::parse_xmldatat($postdata)

    1.$postdata:傳入歐付寶授權回傳的$_POST['XMLData']
> 說明:此函數是作為回傳結果的解密之用， 解密之前需先將回傳結果的空白取代為+<br/>
>      解密後得到一份xml資料，將此xml丟給SimpleXMLElement產生SimpleXMLElement物件，<br/>
>      以利進一步利用。


### Model_Order_Payment_Allpay::update_order($db,SimpleXMLElement $result)

    1.$db: 即libs/libs-mysql.php類別的實體物件。請使用本專案的libs/libs-mysql.php，因為有使用到新增的prefix().
    2.$result: 即歐付寶伺服器回傳的結果，經解密後再轉為SimpleXMLElement的物件傳入.
> 說明:測試流程是以此方法傳回更新訂單的sql，實際上因為已傳入$db，所以可以直接在方法裡面直接執行查詢.<br/>
> 　　 除了訂單編號重複的錯誤之外(RtnCode=10100054)，無論授權成功或失敗都留有更新訂單的敘述.<br/>
> 　　 授權成功的部份保留自訂，沒有決定要寫入什麼，或全部寫入.<br/>
> 　　 授權失敗的部份則是將訂單狀態修改為拒絕訂單.
