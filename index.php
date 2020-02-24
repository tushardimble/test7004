<?php
	$data = array (
  'responseId' => '5b9aba54-91ba-4af6-9d21-7f775e8848a0-7f245a81',
  'queryResult' => 
  array (
    'queryText' => '456 123',
    'action' => 'BalanceRequest.BalanceRequest-yes.BalanceRequest-yes-custom',
    'parameters' => 
    array (
      'Account_Number' => 456123,
    ),
    'allRequiredParamsPresent' => true,
    'fulfillmentText' => 'Please enter the Valid Mobile Number linked to Account11?',
    'fulfillmentMessages' => 
    array (
      0 => 
      array (
        'text' => 
        array (
          'text' => 
          array (
            0 => 'Please enter the Valid Mobile Number linked to Account11?',
          ),
        ),
      ),
    ),
    'outputContexts' => 
    array (
      0 => 
      array (
        'name' => 'projects/bankscustomersupport-mnlxxa/agent/sessions/527c9946-cd74-8e76-f1f8-766df5915f91/contexts/balancerequest-yes-custom-followup',
        'lifespanCount' => 2,
        'parameters' => 
        array (
          'Account_Number.original' => '456 123',
          'Account_Number' => 456123,
        ),
      ),
      1 => 
      array (
        'name' => 'projects/bankscustomersupport-mnlxxa/agent/sessions/527c9946-cd74-8e76-f1f8-766df5915f91/contexts/balancerequest-yes-followup',
        'lifespanCount' => 1,
        'parameters' => 
        array (
          'Account_Number.original' => '456 123',
          'Account_Number' => 456123,
        ),
      ),
    ),
    'intent' => 
    array (
      'name' => 'projects/bankscustomersupport-mnlxxa/agent/intents/a4c4ef15-5fbb-4ab2-91d6-ceae1af4a8a3',
      'displayName' => 'BalanceRequest - yes - AccountNumber',
    ),
    'intentDetectionConfidence' => 0.99899966,
    'diagnosticInfo' => 
    array (
      'webhook_latency_ms' => 72,
    ),
    'languageCode' => 'en',
  ),
  'webhookStatus' => 
  array (
    'code' => 14,
    'message' => 'Webhook call failed. Error: UNAVAILABLE.',
  ),
);
echo json_decode($data);
	
?>
