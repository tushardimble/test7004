<?php

  error_reporting(E_ALL);
  date_default_timezone_set('Asia/Calcutta'); 
  $servername = "66.45.232.178";
  $username = "axisbankcrm1";
  $password = "axisbankcrm1";
  $dbname = "axisbankcrm1";
  $data = array();
// Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }else{
	  echo"Hi";exit;
  }
?>
