<?php
	require("common.php");
	extract($_GET);
	
	$ticketid = explode(" " ,$ticket_number);
	$ticketid = $ticketid[1];
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $basepath."crmapi/checkTicketStatus.php?ticket_number=$ticketid&mobile_number=$mobile_number",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	));

	$response = curl_exec($curl);

	curl_close($curl);
	echo $response;
?>