<?php
error_reporting(0);
	define('DB_SERVER','66.45.232.178');
	define('DB_USER','axisbankcrm');
	define('DB_PASS' ,'axisbankcrm');
	define('DB_NAME', 'axisbankcrm');

 $conn = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
			// Check connection
			if (mysqli_connect_errno()){
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
		 	}else{
				echo"connected";
			}


  
?>
