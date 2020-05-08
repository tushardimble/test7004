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
    		

    		$msg = "Yes";
    	}else{
    		$msg = "No";
    		
    	}
    	
    }
    $data = array (
      'fulfillmentText' => $msg
    );

    $aFinalDialogflowResponse = json_encode($data,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

    echo $aFinalDialogflowResponse;
?>
