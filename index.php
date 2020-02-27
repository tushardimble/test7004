<?php
 $json = file_get_contents('php://input');
  $b = json_decode($json);
  $account_number = $b->queryResult->parameters->Account_Number;
 
  $data = array (
   'fulfillmentText' => $account_number
  );
  $a = json_encode($data);
  print_r($a);
?>
