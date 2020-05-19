<?php
	$request        = file_get_contents('php://input');
    	$requestDecode  = json_decode($request);
	echo"<pre>";print_r($requestDecode);exit;

?>
