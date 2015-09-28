<?php
    if(count($_POST)) {
        $apisign = '';
        foreach($_POST as $key => $value){
            if($key == 'secretkey'){
                $apisign .= $value;
                continue;
            }
            $apisign .= $key . $value;
        }
        $apisign .= 'method123rf.customer.getCreditCount';
        $apisign = md5($apisign);
        
        //Get the number of credit of customer
        $custURL     = 'http://api.123rf.com/rest/?method=123rf.customer.getCreditCount';
        foreach($_POST as $key => $value){
            if($key == 'secretkey'){
                continue;
            }
            $custURL .= "&$key=$value";
        }
        $custURL .= "&apisign=$apisign";
        $custContent = file_get_contents($custURL);
        $custData    = new SimpleXMLElement($custContent);
        if($custData['stat'] == 'ok'){
            echo 'You have ' .$custData->customer->credit['balance']. ' credits.';
        } else { 
            if(preg_match('/signature/', $custData->err)){
                echo 'Invalid Secret Key !' ;
            } else {
                echo $custData->err. ' !';
            }
        }
}