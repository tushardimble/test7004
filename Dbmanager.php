<?php
	error_reporting(0);
	define('DB_SERVER','localhost');
	define('DB_USER','root');
	define('DB_PASS' ,'');
	define('DB_NAME', 'axisbankcrm');

	class DBmanager{
		function __construct(){
			$this->conn = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
			// Check connection
			if (mysqli_connect_errno()){
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
		 	}
		}

		public function getDataByJoin($columnName,$tableName,$condition,$join,$sOrderBy){
			$result	=	mysqli_query($this->conn,"select $columnName from $tableName $join WHERE $condition $sOrderBy");
			$data = array();
			if($result ->num_rows > 0){
				while($row = mysqli_fetch_assoc($result)){
					$data[] = $row;
				}
			}
			return $data;
		}
		public function update($sql){
			
			$result	=	mysqli_query($this->conn,$sql);
			if($result){
				$data="true";
			}else{
				$data = "false";
			}
			return $data;
		}
    }
?>
