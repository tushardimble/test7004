<?php
   header('Content-Type: application/json');
   ob_start();
   $method = $_SERVER['REQUEST_METHOD'];

if($method == 'POST'){
   $input = json_decode(file_get_contents('php://input'), true);
   $param = $input['queryResult']['parameters']['name'];
   switch ($param) {
      
       case 'tushar':
           $speech = "Hi, nice to meet you. This is call from webhook";
       break;

       case 'john':
           $speech = "Good night";
       break;
        
        case 'anything':
           $speech = "Yes type anything here";
       break;

       default:
           $speech = "sorry didn't get.";
           break;
   }
   //$response = new \stdClass();
   //$response->speech= $speech;
   //$response->displayText= $speech;
   //$response->source= "webhook";
   $response = {
  "responseId": "cb6f5ec2-c26e-4349-b561-a9ddd6a0e495",
  "queryResult": {
    "queryText": "actions_intent_CONFIRMATION",
    "action": "Dialogflow action name of matched intent",
    "parameters": {},
    "allRequiredParamsPresent": true,
    "outputContexts": [
      {
        "name": "projects/${PROJECTID}/agent/sessions/${SESSIONID}/contexts/actions_intent_confirmation",
        "parameters": {
          "CONFIRMATION": true
        }
      }
    ],
    "intent": {
      "name": "projects/${PROJECTID}/agent/intents/1777d616-a5f7-4838-a9a9-870f2956bd14",
      "displayName": "Dialogflow action name of matched intent"
    },
    "intentDetectionConfidence": 1,
    "diagnosticInfo": {},
    "languageCode": "en-us"
  },
  "originalDetectIntentRequest": {
    "source": "google",
    "version": "2",
    "payload": {
      "isInSandbox": true,
      "surface": {},
      "inputs": [
        {
          "rawInputs": [
            {
              "query": "yes",
              "inputType": "VOICE"
            }
          ],
          "arguments": [
            {
              "name": "CONFIRMATION",
              "boolValue": true
            }
          ],
          "intent": "actions.intent.CONFIRMATION"
        }
      ],
      "user": {},
      "conversation": {},
      "availableSurfaces": []
    }
  },
  "session": "projects/${PROJECTID}/agent/sessions/${SESSIONID}"
}
   ob_end_clean();
   echo $response;
//    echo json_encode($response);
  }
else
{
    echo "Method not allowed";
}
 
?>
