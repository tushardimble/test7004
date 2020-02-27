<?php
  $json = file_get_contents('php://input');
  $input = json_decode($json,true);
  $account_number = $input['parameters']['Account_Number'];
  $data = array();
  $data = array (
    'fulfillmentText' => $account_number;
  );
  $a = json_encode($data);
  echo $a;
  //echo $json;
?>
