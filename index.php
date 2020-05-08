<?php
	error_reporting(E_ALL);
  	date_default_timezone_set('Asia/Calcutta');

  	$request    	= file_get_contents('php://input');
    $requestDecode  = json_decode($request);
    
    $intent     	= $requestDecode  ->  queryResult ->  intent  ->  displayName;
    $userQueryText  = $requestDecode  ->  queryResult ->  queryText;

    if($intent == "customer"){
    	$current_time 	= 	date("H:i");
    	$current_time   =   strtotime($current_time);

    	$start_time  	= 	"9:00";
    	$start_time    	=   strtotime($start_time);

    	$end_time  		= 	"18:00";
    	$end_time    	=   strtotime($end_time);
    	// "current time ". $current_time ."<br /> Start Time ". $start_time. " <br /> End Time ".$end_time;exit;
    	if($current_time > $start_time && $current_time < $end_time){
    		$demo_request 	= "getButtonText('Demo / Installation Request')";
    		$ticket_status 	= "getButtonText('Status of Tickets')";
    		$raise_cmt 		= "getButtonText('Raise a Complaint')";
    		$comp_warranty 	= "getButtonText('Comprehensive warranty')";

    		$msg = "It is our pleasure to have you as customer. I can help you in doing the following.<br />  <button class='btn-sm btn-primary'  onclick='$demo_request'>Demo / Installation Request</button><br/><button class='btn-sm btn-primary' onclick='$ticket_status'>Status of Tickets</button><br/><button class='btn-sm btn-primary' onclick='$raise_cmt'>Raise a Complaint</button><br><button class='btn-sm btn-primary' onclick='$comp_warranty'>Comprehensive warranty</button>";
    	}else{
    		$msg = "Sorry, Our Operational time is from 9 AM to 6 PM. You can share you phone number and we will give a call.";
    		
    	}
    	
    }else if($intent == "customer_option - custom"){
    	// Check is Agent is Available
    	$sAgentAvailability = "yes";
    	$user_name     = $requestDecode -> queryResult -> parameters -> user_name;
    	if($sAgentAvailability == "yes"){
    		$msg = $user_name." You have been connected to an Agent";
    	}else{
    		$msg = "Sorry ".$user_name." it seems our agent busy. Can you try after sometime. Thank you for our co-operation";
    	}
    }
    $data = array (
      'fulfillmentText' => $msg
    );

    $aFinalDialogflowResponse = json_encode($data,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

    echo $aFinalDialogflowResponse;
?>
