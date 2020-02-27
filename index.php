<?php
define('DB_SERVER','66.45.232.178');
	define('DB_USER','assistant');
	define('DB_PASS' ,'assistant');
	define('DB_NAME','assistant');
 $conn = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
 if (mysqli_connect_errno()){
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}else{
  echo"connected";
 }
 //$json = file_get_contents('php://input');
  //$b = json_decode($json);
  //$account_number = $b->queryResult->parameters->Account_Number;
// if($account_number == "456123"){
 //  $message = "Please Enter mobile number associated with account";
 //}else{
 // $message = "Sorry we could not found any details against this account number";
 //}
 // $data = array (
 //  'fulfillmentText' => $message
  //);
 // $a = json_encode($data);
  
 // print_r($a);
?>
