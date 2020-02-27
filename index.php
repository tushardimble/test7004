<?php

  $request = file_get_contents('php://input');
  $requestDecode = json_decode($request);

  $intent = $requestDecode->queryResult->intent->displayName;
  $account_number = $requestDecode->queryResult->parameters->Account_Number;

  if($intent == "BalanceRequest - yes - AccountNumber"){
    
  }

  if($account_number == "456123"){
  	$message = "Please Enter mobile number associated with account";
  }else{
 	$message = "Sorry we could not found any details against this account number";
  }
  $data = array (
  	'fulfillmentText' => $message
  );
 $aFinalDialogflowResponse = json_encode($data);
  
 echo $aFinalDialogflowResponse;
?>
