<?php
	error_reporting(0)
	include("DBmanager.php");
    $db     =   new DbManager();
	$accountNumber = $_GET['account_number'];

	$columnName = "CONCAT(vcd.firstname,' ',vcd.lastname) AS name , vcscf.cf_864 as account_balance";
	$tableName = "vtiger_contactdetails vcd";
	$condition = "vce.deleted=0 AND vcscf.cf_856=$accountNumber";
	$join = "JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid";
	$sOrderBy = "ORDER BY vcd.contactid DESC";
	$data = $db -> getDataByJoin($columnName,$tableName,$condition,$join,$sOrderBy);

	if(count($data) > 0){
		$response['status'] = 200;
		$response['message'] = "Account Balance";
		$response['data'] = "available";
		echo json_encode($response);
	}else{
		$response['status'] = 400;
		$response['message'] = "Not Found";
		echo json_encode($response);
	}
?>
