<?php

   $input = json_decode(file_get_contents('php://input'), true);
   $param = $input["queryResult"]["queryText"];
   switch ($param) {
      
       case 'Hi':
           $speech = "Hi, nice to meet you";
       break;

       case 'bye':
           $speech = "Good night";
       break;
        
        case 'anything':
           $speech = "Yes type anything here";
       break;

       default:
           $speech = "sorry didn't get.";
           break;
   }
   $response = new \stdClass();
   $response->speech=$speech;
   $response->displayText=$speech;
   $response->source="webhook";
   echo json_encode($response);
   
?>
