<?php
  /*
    Author: Tushar Dimble
    Update:11-march-2020
    Purpose: Write code to connect with CRM DB and provide respose to Dialogflow
  */
  error_reporting(E_ALL);
  date_default_timezone_set('Asia/Calcutta');
  // DB Credentials which is hosting on Plesk Server
  //$servername = "66.45.232.178";
    //$username   = "axisbankcrm3";
    //$password   = "axisbankcrm3";

    $servername = "13.71.118.35";
    $username   = "root";
    $password   = "Amri@951753";
    $dbname   = "axisbankcrm3";
    $data     = array();
    // Connect with Db 
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
      echo"Not";
    }else{
      echo"connected";
    }
?>
