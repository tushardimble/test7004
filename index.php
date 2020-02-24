<?php
	$jsoninput  = file_get_contents('php://input');
	// Converts it into a PHP object
	$data = json_decode($json);
	//echo $data['queryResult']['parameters']['Account_Number'];
	$data1['output'] = "Please Enter valid Mobile Number";
	
?>
