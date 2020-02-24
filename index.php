<?php
$data = array();
	$data = array (
  'fulfillmentText' => 'This is a text response',
  'fulfillmentMessages' => 
  array (
    0 => 
    array (
      'card' => 
      array (
        'title' => 'card title',
        'subtitle' => 'card text',
        'imageUri' => 'https://example.com/images/example.png',
        'buttons' => 
        array (
          0 => 
          array (
            'text' => 'button text',
            'postback' => 'https://example.com/path/for/end-user/to/follow',
          ),
        ),
      ),
    ),
  ),
  'source' => 'example.com',
  'payload' => 
  array (
    'google' => 
    array (
      'expectUserResponse' => true,
      'richResponse' => 
      array (
        'items' => 
        array (
          0 => 
          array (
            'simpleResponse' => 
            array (
              'textToSpeech' => 'this is a simple response',
            ),
          ),
        ),
      ),
    ),
    'facebook' => 
    array (
      'text' => 'Hello, Facebook!',
    ),
    'slack' => 
    array (
      'text' => 'This is a text response for Slack.',
    ),
  ),
  'outputContexts' => 
  array (
    0 => 
    array (
      'name' => 'projects/project-id/agent/sessions/session-id/contexts/context-name',
      'lifespanCount' => 5,
      'parameters' => 
      array (
        'param-name' => 'param-value',
      ),
    ),
  ),
  'followupEventInput' => 
  array (
    'name' => 'event name',
    'languageCode' => 'en-US',
    'parameters' => 
    array (
      'param-name' => 'param-value',
    ),
  ),
);
echo json_encode($data);
	
?>
