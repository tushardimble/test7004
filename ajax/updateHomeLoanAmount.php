<?php
	require("common.php");
	extract($_GET);

	$curl = curl_init();
	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $basepath."crmapi/checkAccount.php?account_number=$account_number",
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
	$response = json_decode($response,true);
	
	if($response['status'] != "200"){
		
		$response['status'] = 400;
		$response['message'] = "Invalid credentials";
		echo json_encode($response);
		exit;
	}
	
	
	$curl1 = curl_init();

	curl_setopt_array($curl1, array(
		CURLOPT_URL => $basepath."crmapi/checkMobileAccount.php?account_number=$account_number&mobile_number=$mobile_number",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
	));

	$balanceresponse = curl_exec($curl1);

	curl_close($curl1);
	$balanceresponse = json_decode($balanceresponse,true);
		
	if($balanceresponse['status'] != "200"){
		$balanceresponse['status'] = 400;
		$balanceresponse['message'] = "Invalid credentials";
		echo json_encode($balanceresponse);
		exit;
	}
	$expectedloanamount = str_replace(' ', '', $expectedloanamount);
	
	$curl2 = curl_init();
	curl_setopt_array($curl2, array(
	  CURLOPT_URL => $basepath."crmapi/updateexpamount.php?account_number=$account_number&mobile_number=$mobile_number&expectedloanamount=$expectedloanamount",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	));

	$response2 = curl_exec($curl2);

	curl_close($curl2);
	$response2 = json_decode($response2,true);
	if($response2['status'] == '200'){
		$response['status'] = 200;
		$response['message'] = "Update";
		$response['data'] = "available";
		echo json_encode($response);
	}else{
		$response['status'] = 400;
		$response['message'] = "something went";
		echo json_encode($response);
	}
	
?>