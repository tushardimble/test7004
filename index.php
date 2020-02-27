<?php
  $json = file_get_contents('php://input');
  $b = json_decode($json);
  $account_number = $b->queryResult->parameters->Account_Number;
  $data = array();
  $data = array (
   'fulfillmentText' => "account number ";
  );
  $a = json_encode($data);
  echo $a;
  //echo $json;
?>
