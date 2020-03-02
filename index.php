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
  }

  $request = file_get_contents('php://input');
  $requestDecode = json_decode($request);
  

  $intent = $requestDecode->queryResult->intent->displayName;

  // For session Id
  $outputContexts = $requestDecode->queryResult->outputContexts[0]->name;
  $outputContextsArray = explode("/", $outputContexts);
  $sessionId = $outputContextsArray[4];
  

  if($sessionId != ""){
    if($intent === "authenticationselection - custom"){
      $account_number = $requestDecode->queryResult->parameters->Account_Number;
      $account_number = str_replace(' ', '', $account_number);
      $mobile_number = $requestDecode->queryResult->parameters->Contact;
      if($account_number != "" && $mobile_number != ""){
        // Check Entered Mobile Number is correct
        $sql = "SELECT vcd.mobile FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856= '$account_number' AND  vcd.mobile='$mobile_number' ORDER BY vcd.contactid DESC";

        $result = $conn->query($sql);
        while($row =mysqli_fetch_assoc($result)) {
          $data[] = $row;
        }

        if(count($data) == 0){
          $message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
          $conn -> close();
        }else{
          
            $sql = "INSERT INTO session_data(sessionId,account_number,mobile_number) VALUES ('$sessionId','$account_number','$mobile_number')";
            $result = $conn->query($sql);
            $message = "I heard your phone number as ".$mobile_number.", is it correct?";
        }
      }
    }else if($intent == "Greeting"){
      
      // Check is session is available
      $aUserData = array();
        $sql = "SELECT * FROM session_data WHERE sessionId = '$sessionId' ORDER BY session_data_id DESC ";
        $result     = $conn->query($sql);
        while($row  = mysqli_fetch_assoc($result)){
          $aUserData[] = $row;
        }

        $current_time = date("h:i a"); 
        //$current_time = "4:00 pm";
        $current_time = strtotime($current_time);
        if($current_time > strtotime("11:59 am") && $current_time < strtotime("3:59 pm")){
          $greeting = "Good Afternoon";
        }else if($current_time > strtotime("3:59 pm") && $current_time < strtotime("11:59 pm")){
          $greeting = "Good Evening";
        }else{
          $greeting = "Good Morning";
        }
       
        if(count($aUserData) > 0 && $aUserData !=""){
          // Delete all previous session
          foreach ($aUserData as $key => $value) {
            $sessionId = $value['sessionId'];
            $sql = "DELETE FROM session_data WHERE sessionId = '$sessionId'";
            
            $result     = $conn->query($sql);
            $message = $greeting." Hi I am Axis bank buddy.  Welcome to Axis bank!. I can speak in English and Hindi, which language would you be more comfortable with.";
          }
        }else{
          $message = $greeting." Hi I am Axis bank buddy.  Welcome to Axis bank!. I can speak in English and Hindi, which language would you be more comfortable with.";
        }
    }else if($intent == "BalanceRequest - yes"){

      // Get Data From Session Id 
      $sql = "SELECT * FROM session_data WHERE sessionId = '$sessionId' ORDER BY session_data_id DESC LIMIT 1";

      $result     = $conn->query($sql);
      $aUserData  = mysqli_fetch_assoc($result);

      if(count($aUserData) != 0 && $aUserData != ""){
        $account_number = $aUserData['account_number'];
        $mobile_number = $aUserData['mobile_number'];
        // Get Account Balance
        $sql = "SELECT CONCAT(vcd.firstname,' ',vcd.lastname) AS name , vcscf.cf_864 as account_balance FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856='$account_number' AND vcd.mobile='$mobile_number' ORDER BY vcd.contactid DESC";
        
        $data = array();
        $result = $conn->query($sql);
        while($row =mysqli_fetch_assoc($result)) {
          $data[] = $row;
        }
        if(count($data) == 0){
          $message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
          $conn -> close();
        }else{
          $message = "Dear ".$data[0]['name'] . ", your account balance is ".$data[0]['account_balance']. " .  What else I can help you with?";
          $conn -> close();
        }
      }else{
        $message = "Dear User tell Enter mobile number and account number";
      }
    }else if($intent == "HomeLoan"){
      // Get Data From Session Id 
      $sql = "SELECT * FROM session_data WHERE sessionId = '$sessionId' ORDER BY session_data_id DESC LIMIT 1";

      $result     = $conn->query($sql);
      $aUserData  = mysqli_fetch_assoc($result);

      if(count($aUserData) != 0 && $aUserData != ""){
        $account_number = $aUserData['account_number'];
        $mobile_number = $aUserData['mobile_number'];
        $home_loan_amount = $requestDecode->queryResult->parameters->HomeLoanAmount;
        if($home_loan_amount != "" && $account_number != "" && $mobile_number != ""){
          $sql = "SELECT vcd.mobile FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856= '$account_number' AND  vcd.mobile='$mobile_number' ORDER BY vcd.contactid DESC";
          $data = array();
          $result = $conn->query($sql);
          while($row =mysqli_fetch_assoc($result)) {
            $data[] = $row;
          }
          
          if(count($data) == 0){
            $message = "Sorry we could not find any details against this account number and mobile number , What else I can help you with?";
            $conn -> close();
          }else{
            // Update Home Loan Amount
            $home_loan_amount = str_replace(' ', '', $home_loan_amount);
            $sql = "UPDATE vtiger_contactscf SET cf_860='$home_loan_amount' WHERE cf_856= $account_number";
            $result = $conn->query($sql);
            $message = "Thank you for the details! I have passed on the details to our team, and one of our representative would reach out to you shortly to help you out. What else I can help you with?";
            $conn -> close();
          }
        }else{
          $message = "Dear User Please tell home loan amount.";
        }
      }else{
        $message = "Dear User tell Enter mobile number and account number";
      }
    }else if($intent == "openFDaccount"){
      
      // Get Data From Session Id 
      $sql = "SELECT * FROM session_data WHERE sessionId = '$sessionId' ORDER BY session_data_id DESC LIMIT 1";
      $result     = $conn->query($sql);
      $aUserData  = mysqli_fetch_assoc($result);
      //echo"<pre>";print_r($aUserData);
      if(count($aUserData) != 0 && $aUserData != ""){
        $account_number = $aUserData['account_number'];
        $mobile_number = $aUserData['mobile_number'];
        $fd_amount        = $requestDecode->queryResult->parameters->FDAmount;
        $locking_period   = $requestDecode->queryResult->parameters->LockingPeriod;
        if($fd_amount != "" && $locking_period != "" && $account_number != "" && $mobile_number != ""){
          // Check Check Mobile Number and Account Number(Combined Check)

          $sql = "SELECT vcd.mobile FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856= '$account_number' AND  vcd.mobile='$mobile_number' ORDER BY vcd.contactid DESC";
          $data = array();
          $result = $conn->query($sql);
          while($row =mysqli_fetch_assoc($result)) {
            $data[] = $row;
          }
          
          if(count($data) == 0){
            $message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
            $conn -> close();
          }else{
            // Update FD Amount
            $fd_amount = str_replace(' ', '', $fd_amount);
            $locking_period = str_replace(' ', '', $locking_period);
            $sql = "UPDATE vtiger_contactscf SET cf_866='$fd_amount' , cf_868='$locking_period' WHERE cf_856= $account_number";
            $result = $conn->query($sql);
            $message = "Thank you for the details! I have passed on the details to our team, and one of our representative would reach out to you shortly to help you out with the various Fixed Deposit rates and options. What else I can help you with?";
            $conn -> close();
          }
        }else{
          $data['followupEventInput']['name'] ="recall";
          $a = json_encode($data);
          //$message = "Dear User tell Enter mobile number and account number";
          echo $a;exit;
        }
      }else{
        
        $data['followupEventInput']['name'] ="recall";
          $a = json_encode($data);
          //$message = "Dear User tell Enter mobile number and account number";
          echo $a;exit;
      }
        
    }else if($intent == "TicketDetails"){

      $ticket_number   = $requestDecode->queryResult->parameters->TicketNumber;
      $mobile_number    = $requestDecode->queryResult->parameters->Contact;
      if($ticket_number != "" && $mobile_number != ""){
        $sql = "SELECT CONCAT(vcd.firstname,' ',vcd.lastname) AS name,vtt.status FROM vtiger_troubletickets vtt JOIN vtiger_crmentity vce ON vtt.ticketid = vce.crmid JOIN vtiger_contactdetails vcd ON vtt.contact_id = vcd.contactid WHERE vce.deleted='0' AND vcd.mobile='$mobile_number' AND vtt.ticketid='$ticket_number' ORDER BY vtt.ticketid DESC";
        $data = array();
        $result = $conn->query($sql);
        while($row =mysqli_fetch_assoc($result)) {
          $data[] = $row;
        }
        
        if(count($data) == 0){
          $message = "Sorry we could not find any details against this Ticket number and Mobile number. What else I can help you with?";
          $conn -> close();
        }else{
          
          $message = "Dear ". $data[0]['name'] .",current status of your ticket ".$ticket_number ." is ". $data[0]['status'].". What else I can help you with?";
          $conn -> close();
        }
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
