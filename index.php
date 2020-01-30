<?php

   $input = json_decode(file_get_contents('php://input'), true);
   $param = $input["result"]["parameters"]["text"];
   echo $param;exit;
   
?>
