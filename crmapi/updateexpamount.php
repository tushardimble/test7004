<?php
	
	include("DBmanager.php");
    $db     =   new DbManager();
	$mobileNumber = $_GET['mobile_number'];
	$accountNumber = $_GET['account_number'];
	$expectedloanamount = $_GET['expectedloanamount'];
	$sql = "UPDATE vtiger_contactscf SET cf_860='$expectedloanamount' WHERE cf_856= $accountNumber";
	//echo $sql;
	$data = $db -> update($sql);
	
	if($data=="true"){
		$response['status'] = 200;
		$response['message'] = "Update";
		$response['data'] = "available";
		echo json_encode($response);
	}else{
		$response['status'] = 400;
		$response['message'] = "Not Found";
		echo json_encode($response);
	}
	
	
?>