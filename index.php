<?php
  $connection = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
  $data = array();
  $data = array (
    'fulfillmentText' => 'hI THIS IS TEST'
  );
  $a = json_encode($data);
  echo $a; 
?>
