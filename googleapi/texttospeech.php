<?php
	extract($_POST);
	//echo"<pre>";print_r($_POST);exit;
	$curl = curl_init();

	$postdata['input']['text'] = $text;
	$postdata['voice']['languageCode'] = "en-GB";
	//$postdata['voice']['languageCode'] = "hi-IN";
	$postdata['voice']['name'] = "en-GB-Standard-B";
	// $postdata['voice']['ssmlGender'] = "FEMALE";
	//$postdata['voice']['name'] = "hi-IN-Standard-B";
	$postdata['voice']['ssmlGender'] ="MALE";
	$postdata['audioConfig']['audioEncoding'] = "MP3";
	$postdata = json_encode($postdata);

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://texttospeech.googleapis.com/v1/text:synthesize?key=AIzaSyClrEIMBNqPuOzbf19TI7u1p0nyxTb6Lls",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => $postdata,
	  CURLOPT_HTTPHEADER => array(
	    "Content-Type: application/json"
	  ),
	));

	$response = curl_exec($curl);

	curl_close($curl);
	$googleResponse  = json_decode($response,true);
	
	$data['status'] = 200;
	$data['message'] = "Google Voice";
	$data['data'] = $googleResponse['audioContent'];
	echo json_encode($data);
?>

