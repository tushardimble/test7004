<?php
	include("DBmanager.php");
    $db     =   new DbManager();
	$account_number = $_GET['account_number'];
	$mobileNumber = $_GET['mobile_number'];
	$columnName = "CONCAT(vcd.firstname,' ',vcd.lastname) AS name , vcscf.cf_864 as account_balance";
	$tableName = "vtiger_contactdetails vcd";
	$condition = "vce.deleted=0 AND vcscf.cf_856= $account_number AND  vcd.mobile=$mobileNumber";
	$join = "JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid";
	$sOrderBy = "ORDER BY vcd.contactid DESC";
	$data = $db -> getDataByJoin($columnName,$tableName,$condition,$join,$sOrderBy);

	if(count($data) > 0){
		$response['status'] = 200;
		$response['message'] = "Account Balance";
		$response['data'] = $data;
		echo json_encode($response);
	}else{
		$response['status'] = 400;
		$response['message'] = "Not Found";
		echo json_encode($response);
	}
?>