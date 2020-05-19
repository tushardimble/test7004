<?php
    error_reporting(E_ALL);
    date_default_timezone_set('Asia/Calcutta');

    $request        = file_get_contents('php://input');
    $requestDecode  = json_decode($request,true);
    
    $fromNumber 	= $requestDecode['payload']['source'];
    $incomingMsg 	= $requestDecode['payload']['payload']['text'];

    echo $fromNumber;echo $incomingMsg;
?>
