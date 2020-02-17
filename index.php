<?php
	date_default_timezone_set('Asia/Calcutta'); 
	$current_time = date("h:i a"); 
	//$current_time = "4:00 pm";
	$current_time = strtotime($current_time);
	if($current_time > strtotime("11:59 am") && $current_time < strtotime("3:59 pm")){
		$greeting = "Good Afternoon";
	}else if($current_time > strtotime("3:59 pm") && $current_time < strtotime("11:59 pm")){
		$greeting = "Good Evening";
	}else{
		$greeting = "Good Morning";
	}
	//echo $_SERVER['HTTP_HOST'];
	
?>
<!DOCTYPE html>
<html>
	<head>
      <title>Voice Recognition</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <link rel="stylesheet" href="assets/css/style.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
      <!-- <script src="other/bower_components/platform/platform.js"></script> -->
      <script src="other/src/webspeech.js"></script>
	</head>
	<body style="background-image: url('assets/images/axis.png');background-size: cover;">
		<select id="lang" style="display: none">
			<option value="en" selected>English</option>
		</select>
		<div class="chatbox-holder">
			<div class="chatbox">
				<div class="chatbox-top">
					<div class="chatbox-avatar">
						<img src="assets/images/logo.png" />
					</div>
					<div class="chat-partner-name">
					  <span class="status online"></span>
					  Axis Bank
					</div>
					<div class="chatbox-icons">
					  <a href="javascript:void(0);"><i class="fa fa-minus"></i></a>
					</div>
				</div>
				<div class="chat-messages" id="conversation">
					<div class="message-box-holder">
						<p class="message-box" id="welcomegreeting">
						 	<?php echo $greeting;?>, Welcome to Axis Bank
						</p>
					</div>
				   
				</div>
				<div class="chat-input-holder">
					<input type="text" class="chat-input" id="typetext">
					<button class="message-send" onclick="typingText();"><i class="fa fa-paper-plane" id="type" style="font-size:20px;color: white"></i></button>&nbsp
					<button class="message-send" onclick="start();"><i class="fa fa-microphone" id="mike" style="font-size:24px;color: white"></i></button> 
				   <!-- <input type="button" value="Send" class="message-send fa fa-microphone" /> -->
				</div>
				
				<input type="hidden" id="selectedlanguage">
			</div>
		</div>
		<script src="assets/js/custom.js"></script>
		<script>
      navigator.mediaDevices.getUserMedia({ audio: true })
      .then(function(stream) {
        console.log('You let me use your mic!')
      })
      .catch(function(err) {
        console.log('No mic for you!')
      });
    </script>
   </body>
</html>
