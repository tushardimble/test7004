<?php
  $json = file_get_contents('php://input');
  $b = json_decode($json);
  //$account_number = $b->queryResult->queryText;
 
  $data = array();
  $data = array (
   'fulfillmentText' => "Hiiii";
  );
  $a = json_encode($data);
  echo $a;
?>
