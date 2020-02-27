<?php
 $json = file_get_contents('php://input');
  $b = json_decode($json);
  $account_number = $b->queryResult->parameters->Account_Number;
 if($account_number == "456123"){
   $message = "Please Enter mobile number associated with account";
 }else{
  $message = "Sorry we could not found any details against this account number";
 }
  $data = array (
   'fulfillmentText' => $account_number
  );
  $a = json_encode($data);
  
  print_r($a);
?>
