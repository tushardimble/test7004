<?php
  $json = file_get_contents('php://input');
  $b = json_decode($json);
  //$account_number = $b->queryResult->queryText;
 
  echo $json;
?>
