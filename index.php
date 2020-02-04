<?php
   header('Content-Type: application/json');
   ob_start();
   $method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST'){
   $input = json_decode(file_get_contents('php://input'), true);
   $param = $input['result']['parameters']['text'];
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
   $response->speech= $speech;
   $response->displayText= $speech;
   $response->source= "webhook1";
   
   ob_end_clean();
   
    echo json_encode($response);
  }
else
{
    echo "Method not allowed";
}
 
?>
