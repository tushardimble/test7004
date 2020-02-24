<?php
$data = array();
	$data = array (
  'responseId' => '352c3678-4b31-4a2e-b35e-dd9d667d7ce1-7f245a81',
  'queryResult' => 
  array (
    'queryText' => '456 123',
    'action' => 'BalanceRequest.BalanceRequest-yes.BalanceRequest-yes-custom',
    'parameters' => 
    array (
      'Account_Number' => 456123,
    ),
    'allRequiredParamsPresent' => true,
    'fulfillmentText' => 'Please enter the Valid Mobile Number linked to Account111?',
    'messages' => 
    array (
      0 => 
      array (
        'text' => 
        array (
          'text' => 
          array (
            0 => 'Please enter the Valid Mobile Number linked to Account111?',
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
          'Account_Number' => 456123,
          'Account_Number.original' => '456 123',
        ),
      ),
      1 => 
      array (
        'name' => 'projects/bankscustomersupport-mnlxxa/agent/sessions/527c9946-cd74-8e76-f1f8-766df5915f91/contexts/balancerequest-yes-followup',
        'lifespanCount' => 1,
        'parameters' => 
        array (
          'date.original' => '',
          'Balance' => 'balance',
          'Account_Number.original' => '456 123',
          'Balance.original' => 'balance',
          'Account_Number' => 456123,
          'date' => '',
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
      'webhook_latency_ms' => 57,
    ),
    'languageCode' => 'en',
  ),
  'webhookStatus' => 
  array (
    'code' => 14,
    'message' => 'Webhook call failed. Error: UNAVAILABLE.',
  ),
);
echo json_encode($data);
	
?>
