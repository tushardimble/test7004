<?php
   $method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST'){
   $input = json_decode(file_get_contents('php://input'), true);
   $param = $input['queryResult']['parameters']['text'];
   switch ($param) {
      
       case 'hi':
           $speech = "Hi, nice to meet you. This is call from webhook";
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
   $response->result->fulfillment->speech=$speech;
   $response->displayText=$speech;
   $response->source="webhook";
   echo json_encode($response);
  }
else
{
    echo "Method not allowed";
}
 
?>
