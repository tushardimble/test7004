<?php
	include("DBmanager.php");
    $db     =   new DbManager();
	$ticket_number = $_GET['ticket_number'];
	$mobile_number = $_GET['mobile_number'];

	
	$columnName = "CONCAT(vcd.firstname,' ',vcd.lastname) AS name,vtt.status";
	$tableName = "vtiger_troubletickets vtt";
	$condition = "vce.deleted='0' AND vcd.mobile='$mobile_number' AND vtt.ticketid=$ticket_number";
	$join = "JOIN vtiger_crmentity vce ON vtt.ticketid = vce.crmid JOIN vtiger_contactdetails vcd ON vtt.contact_id = vcd.contactid";
	$sOrderBy = "ORDER BY vtt.ticketid DESC";
	$data = $db -> getDataByJoin($columnName,$tableName,$condition,$join,$sOrderBy);

	if(count($data) > 0){
		$response['status'] = 200;
		$response['message'] = "Ticket Data";
		$response['data'] = $data;
		echo json_encode($response);
	}else{
		$response['status'] = 400;
		$response['message'] = "Invalid credentials";
		echo json_encode($response);
	}
?>