const messages = document.getElementById('conversation');
var recognition = new webkitSpeechRecognition();
recognition.continuous = true;
recognition.lang = 'en-US';

var input = document.getElementById("typetext");
input.addEventListener("keyup", function(event) {
	if (event.keyCode === 13) {
		event.preventDefault();
		document.getElementById("type").click();
		$('#typetext').val("");
	}
});


$(function() {
	$('.fa-minus').click(function() {
		$(this).closest('.chatbox').toggleClass('chatbox-min');
	});
	$('.fa-close').click(function() {
		$(this).closest('.chatbox').hide();
	});
});

// Random Id Genaration
function makeid(length) {
	var result = '';
	var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	var charactersLength = characters.length;
	for (var i = 0; i < length; i++) {
		result += characters.charAt(Math.floor(Math.random() * charactersLength));
	}
	return result;
}
// End Random Id Generation

function scrollToBottom() {
	messages.scrollTop = messages.scrollHeight;
}

function start() {

	$("#mike").css("color", "red");
	var paraId = makeid(5);
	
	$("#conversation").append("<div class='message-box-holder'><p class='message-box message-partner' id='" + paraId + "'></div>");

	shouldScroll = messages.scrollTop + messages.clientHeight === messages.scrollHeight;

	if (!shouldScroll) {
		scrollToBottom();
	}

	recognition.onresult = function(event) {
		console.log(event);
		var output = document.getElementById(paraId);
		output.innerHTML = "";
		for (var i = 0; i < event.results.length; i++) {
			output.innerHTML = output.innerHTML + event.results[i][0].transcript;
		}
		callDialogFlow(paraId);
		
	}
	recognition.start();

}

function typingText(){
	var paraId = makeid(5);
	var typetext = $('#typetext').val();
	if(typetext != ""){
		$("#conversation").append("<div class='message-box-holder'><p class='message-box message-partner' id='" + paraId + "'>"+ typetext + "</div>");
		$('#typetext').val("");
		callDialogFlow(paraId);
	}else{
		alert("Please Enter some text");
	}
	
}

var sessionId1 = makeid(8);
var sessionId2 = makeid(4);
var sessionId3 = makeid(4);
var sessionId4 = makeid(4);
var sessionId5 = makeid(12);

function start() {

	$("#mike").css("color", "red");
	var paraId = makeid(5);
	
	$("#conversation").append("<div class='message-box-holder'><p class='message-box message-partner' id='" + paraId + "'></div>");

	shouldScroll = messages.scrollTop + messages.clientHeight === messages.scrollHeight;

	if (!shouldScroll) {
		scrollToBottom();
	}

	recognition.onresult = function(event) {
		console.log(event);
		var output = document.getElementById(paraId);
		output.innerHTML = "";
		for (var i = 0; i < event.results.length; i++) {
			output.innerHTML = output.innerHTML + event.results[i][0].transcript;
		}
		callDialogFlow(paraId);
		
	}
	recognition.start();

}

function callDialogFlow(paraId){
	
	shouldScroll = messages.scrollTop + messages.clientHeight === messages.scrollHeight;

	if (!shouldScroll) {
		scrollToBottom();
	}

	var paratext = document.getElementById(paraId).innerHTML;	
	var selectedlanguage = $("#selectedlanguage").val();	
	

	if(paratext != ""){
		var settings = {
			"url": "https://api.dialogflow.com/v1/query?v=20150910",
			"method": "POST",
			"timeout": 0,
			"headers": {
				//"Authorization": " Bearer 21f4a4f89b3346feb5fd951eb5bdcf2b",
				"Authorization": "Bearer def2d5bbf5d942deaa7ea2bace437da9",
				"Content-Type": "application/json"
			},
			"data": JSON.stringify({
				"contexts": ["shop"],
				//"lang": "hi",
				"lang": "en",
				"query": paratext,
				//"sessionId": "976280e7-7ff7-a1ef-5539-"+sessionId,
				"sessionId":sessionId1+"-"+sessionId2+"-"+sessionId3+"-"+sessionId4+"-"+sessionId5,
				"timezone": "America/New_York"
			}),
		};

		//var paraId1 = makeid(5);
		$.ajax(settings).done(function(response) {
			var paraId1 = makeid(5);
			var text = response['result']['fulfillment']['speech'];
			var intent = response['result']['metadata']['intentName'];
			
			if (intent == "BalanceRequest - yes - AccountNumber") {
				var accountNumber = response['result']['parameters']['Account_Number'];

				if (accountNumber != "") {

					$.ajax({
						type: "GET",
						url: "ajax/checkAccount.php?account_number=" + accountNumber,
						async: false,
						success: function(response) {
							var result = JSON.parse(response);
							console.log(response);
							if (result['status'] == '200') {
								//var customText = "Please Enter your m";
								$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + text + "</div>");
								textToSpeech(text);
							} else {
								var customText = "Invalid User";
								$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + customText + "</div>");
								textToSpeech(customText);
							}
						}
					});
				} else {

					$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + text + "</div>");
					textToSpeech(text);
				}
			}else if(intent == "LanguageSelection"){
				var language = response['result']['parameters']['Language'];
				if(language != ""){
					// Check browser support
					if (typeof(Storage) !== "undefined") {
					  // Store
					  sessionStorage.setItem("language",language);
					  // Retrieve
					  document.getElementById("selectedlanguage").value = sessionStorage.getItem("language");
					  $("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + text + "</div>");
							textToSpeech(text);
					}
				}
			}else if (intent == "BalanceRequest - yes - AccountNumber - PhoneNumber") {
				var mobileNumber = response['result']['parameters']['Contact'];
				if (mobileNumber != "") {
					$.ajax({
						type: "GET",
						url: "ajax/callapi.php?mobile_number=" + mobileNumber,
						async: false,
						success: function(response) {
							var result = JSON.parse(response);
							console.log(result);
							if (result['status'] == '200') {
								var customText = "Dear " + result['data'][0]['name'] + " your account balance is " + result['data'][0]['account_balance'];
							} else {
								var customText = "Invalid User";
							}
							$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + customText + "</div>");
							textToSpeech(customText);
						}
					});
				} else {
					$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + text + "</div>");
					textToSpeech(text);
				}
			} else if (intent == "HomeLoan") {
				var expectedloanamount = response['result']['parameters']['HomeLoanAmount'];
				var account_number = response['result']['parameters']['Account_Number'];
				var mobile_number = response['result']['parameters']['Contact'];
				if (expectedloanamount != "" && account_number != "" && mobile_number != "") {

					$.ajax({
						type: "GET",
						url: "ajax/updateHomeLoanAmount.php?account_number=" + account_number + "&expectedloanamount=" + expectedloanamount.trim() + "&mobile_number=" + mobile_number,
						async: false,
						success: function(response) {
							var result = JSON.parse(response);
							console.log(result);
							if (result['status'] == '200') {
								var customText = "Thank for information,our agent will call you.";
							} else {
								var customText = result['message'];
							}
							$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + customText + "</div>");
							textToSpeech(customText);
						}
					});
				} else {

					$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + text + "</div>");
					textToSpeech(text);
				}

			} else if (intent == "FixedDeposit") {
				var fdamount = response['result']['parameters']['FDAmount'];
				var lockingperiods = response['result']['parameters']['LockingPeriod'];
				var mobile_number = response['result']['parameters']['Contact'];
				var account_number = response['result']['parameters']['Account_Number'];
			
				if (fdamount != "" && lockingperiods != "" && mobile_number != "" && account_number) {
					$.ajax({
						type: "GET",
						url: "ajax/updateFdAmount.php?account_number=" + account_number + "&fdamount=" + fdamount.trim() + "&mobile_number=" + mobile_number + "&lockingperiods=" + lockingperiods,
						async: false,
						success: function(response) {
							var result = JSON.parse(response);
							console.log(result);
							if (result['status'] == '200') {
								var customText = "Thank for information,our agent will call you.";
							} else {
								var customText = result['message'];
							}
							$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + customText + "</div>");
							textToSpeech(customText);
						}
					});
				} else {
					$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + text + "</div>");
					textToSpeech(text);
				}
			} else if(intent == "TicketDetails"){
				var ticket_number = response['result']['parameters']['TicketNumber'];
				var mobile_number = response['result']['parameters']['Contact'];
				if(ticket_number != "" && mobile_number != ""){
					// Check Ticket Status
					$.ajax({
						type: "GET",
						url: "ajax/checkMyTicketStatus.php?ticket_number=" + ticket_number + "&mobile_number=" + mobile_number,
						async: false,
						success: function(response) {
							var result = JSON.parse(response);
							console.log(result);
							if (result['status'] == '200') {
								var ticketStatus = result['data'][0]['status'];
								var ticketcustomerName = result['data'][0]['name'];
								var customText = "Dear "+ticketcustomerName+" your ticket " +ticket_number + " is "+ticketStatus;
							} else {
								var customText = result['message'];
							}
							$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + customText + "</div>");
							textToSpeech(customText);
						}
					});
				} else {

					$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + text + "</div>");
					textToSpeech(text);
				}
			}else {
				$("#conversation").append("<div class='message-box-holder'><p class='message-box' id='" + paraId1 + "'>" + text + "</div>");
				textToSpeech(text);
			}


			if (!shouldScroll) {
				scrollToBottom();
			}
			$("#mike").css("color", "white");
			
		});
	}else{
		alert("Something went wrong");
	}
	
}

function textToSpeech(message){
	$.ajax({
		type: "POST",
		url: "googleapi/texttospeech.php",
		data: {text:message},
		async: false,
		success: function(response) {
			var result = JSON.parse(response);
			
			var base64string =result['data'];
			console.log(base64string);
			var snd = new Audio("data:audio/mp3;base64," + base64string);
			snd.play();
			recognition.stop();
		}
	});
	//console.log(message)
}


            
