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
 
  $intent = $requestDecode->queryResult->intent->displayName;
  
  if($intent == "BalanceRequest - yes - AccountNumber"){
    $account_number = $requestDecode->queryResult->parameters->Account_Number;
    $account_number = str_replace(' ', '', $account_number);

    $sql = "SELECT CONCAT(vcd.firstname,' ',vcd.lastname) AS name , vcscf.cf_864 as account_balance FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856='$account_number' ORDER BY vcd.contactid DESC";
    
    $result = $conn->query($sql);
    while($row =mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }
    
    if(count($data) > 0){
      $message = "Please Tell mobile number associated with account";
    }else{
      $message = "Sorry we could not found any details against this account number";
    }

  }else if($intent == "BalanceRequest - yes - AccountNumber - PhoneNumber"){

    $mobile_number = $requestDecode->queryResult->parameters->Contact;

    $sql = "SELECT CONCAT(vcd.firstname,' ',vcd.lastname) AS name , vcscf.cf_864 as account_balance FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcd.mobile='$mobile_number'ORDER BY vcd.contactid DESC";
    //echo $sql;exit;
    $result = $conn->query($sql);
    while($row =mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }

    if(count($data) == 0){
      $message = "Sorry we could not found any details against this account number and mobile number.";
    }else{
      $message = "Dear ".$data[0]['name'] . " your account balance is ".$data[0]['account_balance'];
    }

  }else if($intent == "HomeLoan"){

    $home_loan_amount = $requestDecode->queryResult->parameters->HomeLoanAmount;
    $account_number   = $requestDecode->queryResult->parameters->Account_Number;
    $mobile_number    = $requestDecode->queryResult->parameters->Contact;
    
    if($home_loan_amount != "" && $account_number != "" && $mobile_number != ""){
      // Check Check Mobile Number and Account Number(Combined Check)

      $sql = "SELECT vcd.mobile FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856= '$account_number' AND  vcd.mobile='$mobile_number' ORDER BY vcd.contactid DESC";
      $data = array();
      $result = $conn->query($sql);
      while($row =mysqli_fetch_assoc($result)) {
        $data[] = $row;
      }
      
      if(count($data) == 0){
        $message = "Sorry we could not found any details against this account number and mobile number.";
      }else{
        // Update Home Loan Amount
        $home_loan_amount = str_replace(' ', '', $home_loan_amount);
        $sql = "UPDATE vtiger_contactscf SET cf_860='$home_loan_amount' WHERE cf_856= $account_number";
        $result = $conn->query($sql);
        $message = "Thank you for information,our agent will call you.";
      }
    }
  }else if($intent == "FixedDeposit"){
    $fd_amount        = $requestDecode->queryResult->parameters->FDAmount;
    $locking_period   = $requestDecode->queryResult->parameters->LockingPeriod;
    $account_number   = $requestDecode->queryResult->parameters->Account_Number;
    $mobile_number    = $requestDecode->queryResult->parameters->Contact;

    if($fd_amount != "" && $locking_period != "" && $account_number != "" && $mobile_number != ""){
      // Check Check Mobile Number and Account Number(Combined Check)

      $sql = "SELECT vcd.mobile FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856= '$account_number' AND  vcd.mobile='$mobile_number' ORDER BY vcd.contactid DESC";
      $data = array();
      $result = $conn->query($sql);
      while($row =mysqli_fetch_assoc($result)) {
        $data[] = $row;
      }
      
      if(count($data) == 0){
        $message = "Sorry we could not found any details against this account number and mobile number.";
      }else{
        // Update FD Amount
        $fd_amount = str_replace(' ', '', $fd_amount);
        $locking_period = str_replace(' ', '', $locking_period);
        $sql = "UPDATE vtiger_contactscf SET cf_866='$fd_amount' , cf_868='$locking_period' WHERE cf_856= $account_number";
        $result = $conn->query($sql);
        $message = "Thank you for information,our agent will call you.";
      }
    }
  }
  // Dialogflow Response
  $data = array (
  	'fulfillmentText' => $message
  );

  $aFinalDialogflowResponse = json_encode($data);
  
  echo $aFinalDialogflowResponse;
?>
