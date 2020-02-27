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

  $request = file_get_contents('php://input');
  $requestDecode = json_decode($request);
 // echo"<pre>";print_r($requestDecode);exit;
  $intent = $requestDecode->queryResult->intent->displayName;
  $account_number = $requestDecode->queryResult->parameters->Account_Number;
 
  


  if($intent == "BalanceRequest - yes - AccountNumber"){
    $account_number = str_replace(' ', '', $account_number);
    $sql = "SELECT CONCAT(vcd.firstname,' ',vcd.lastname) AS name , vcscf.cf_864 as account_balance FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856='$account_number' ORDER BY vcd.contactid DESC";
    
    $result = $conn->query($sql);
    while($row =mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }
    
    if(count($data) > 0){
      $message = "Please Enter mobile number associated with account";
    }else{
      $message = "Sorry we could not found any details against this account number";
    }
  }
  // Dialogflow Response
  $data = array (
  	'fulfillmentText' => $message
  );

  $aFinalDialogflowResponse = json_encode($data);
  
  echo $aFinalDialogflowResponse;
?>
