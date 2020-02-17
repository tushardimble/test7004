<?php
	
	include("DBmanager.php");
    $db     =   new DbManager();
	$lockingperiods = $_GET['lockingperiods'];
	$accountNumber = $_GET['account_number'];
	$fdamount = $_GET['fdamount'];
	$sql = "UPDATE vtiger_contactscf SET cf_866='$fdamount' , cf_868='$lockingperiods' WHERE cf_856= $accountNumber";
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