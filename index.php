<?php
  error_reporting(E_ALL);
  $servername = "66.45.232.178";
  $username = "axisbankcrm";
  $password = "axisbankcrm";
  $dbname = "axisbankcrm";

// Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

 //  $request = file_get_contents('php://input');
 //  $requestDecode = json_decode($request);
 // // echo"<pre>";print_r($requestDecode);exit;
 //  $intent = $requestDecode->queryResult->intent->displayName;
 //  $account_number = $requestDecode->queryResult->parameters->Account_Number;
 
  


 // if($intent == "BalanceRequest - yes - AccountNumber"){
    $sql = "SELECT * FROM vtiger_contactdetails";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
      $data[] = $row;
    }
    echo"<pre>";print_r($data);exit;
    if(count($data) > 0){
      $message = "Please Enter mobile number associated with account";
    }else{
      $message = "Sorry we could not found any details against this account number";
    }
  //}
  // Dialogflow Response
  $data = array (
  	'fulfillmentText' => $message
  );

  $aFinalDialogflowResponse = json_encode($data);
  
  echo $aFinalDialogflowResponse;
?>
