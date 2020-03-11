<?php
	/*
		Author: Tushar Dimble
		Update:11-march-2020
		Purpose: Write code to connect with CRM DB and provide respose to Dialogflow
	*/
	error_reporting(0);
	date_default_timezone_set('Asia/Calcutta');
	// DB Credentials which is hosting on Plesk Server
	$servername = "66.45.232.178";
  	$username 	= "axisbankcrm1";
  	$password 	= "axisbankcrm1";
  	$dbname 	= "axisbankcrm1";
  	$data 		= array();
  	// Connect with Db 
  	$conn = new mysqli($servername, $username, $password, $dbname);

  	// Check connection
  	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
  	}

  	// Get Json Input from Dialogflow
  	$request 		= file_get_contents('php://input');
  	$requestDecode 	= json_decode($request);

  	$intent 		= $requestDecode	->	queryResult	->	intent 	-> 	displayName;
  	$languageCode 	= $requestDecode 	-> 	queryResult -> 	languageCode;

  	// Get Session Id
  	$outputContexts 		= 	$requestDecode -> queryResult -> outputContexts[0] -> name;
  	$outputContextsArray 	= 	explode("/", $outputContexts);
  	$sessionId 				= 	$outputContextsArray[4];

  	if($sessionId != ""){
  		/* Part 1
  			Check is session already exist
  		*/
  		$sessionSql = "SELECT * FROM session_data WHERE sessionId = '$sessionId' ORDER BY session_data_id DESC LIMIT 1";
  		$result     = $conn->query($sessionSql);
  		$aUserData  = mysqli_fetch_assoc($result);
  		
  		if(count($aUserData) != 0 && $aUserData != ""){
  			if($aUserData['is_validate'] == 1){
  				$isSessionAvailable = "Yes";
  			}else{
  				$isSessionAvailable = "NotValidate";
  			}
  		}else{
  			$isSessionAvailable = "No";
  		}

  		/*
  			Part 2 
  			Check Account number and mobile in system
  		*/
  		if($intent == "HomeLoan" || $intent == "openFDaccount" || $intent == "TicketDetails"){
  			if($isSessionAvailable == "Yes"){
  				$account_number = $aUserData['account_number'];
        		$mobile_number = $aUserData['mobile_number'];
	  			$sql = "SELECT CONCAT(vcd.firstname,' ',vcd.lastname) AS name , vcscf.cf_864 as account_balance FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856='$account_number' AND vcd.mobile='$mobile_number' ORDER BY vcd.contactid DESC";
	        	$result = $conn->query($sql);
		        while($row =mysqli_fetch_assoc($result)) {
		          $data[] = $row;
		        }

		        if(count($data) == 0){
		        	$accountAndMobileNumberExist = "No";
		        }else{
		        	$accountAndMobileNumberExist = "Yes";
		        }
		    }else if($isSessionAvailable == "No"){
		    	$data['followupEventInput']['name'] ="recall";
		        $data['followupEventInput']['parameters']['Account_Number']='';
		        $data['followupEventInput']['parameters']['Contact']='';
		        $data['languageCode']= "en-US";
		        $aBlankDetails = json_encode($data);
		        echo $aBlankDetails;exit;
		    }else{
  				// If OTP is wrong then reenter OTP
    			$data['followupEventInput']['name'] = "recallotp";
		        $data['followupEventInput']['parameters']['OTP'] = '';
		        $data['languageCode'] = "en-US";
		        $aBlankDetails = json_encode($data);
		        echo $aBlankDetails;exit;
  			}
  		}
  		/* Part 3
  			Intent wise code
  		*/
  		if($intent === "authenticationselection - custom" || $intent === "add_details"){
  			$account_number = $requestDecode->queryResult->parameters->Account_Number;
      		$account_number = str_replace(' ', '', $account_number);
      		$mobile_number = $requestDecode->queryResult->parameters->Contact;
      		$mobile_number = str_replace('-', '', $mobile_number);
      		$mobile_number = str_replace(' ', '', $mobile_number);

      		if($account_number != "" && $mobile_number != ""){
      			// Check Entered Mobile Number is correct
        		 
        		$sql = "SELECT vcd.mobile FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856= '$account_number' AND  vcd.mobile='$mobile_number' ORDER BY vcd.contactid DESC";
        		
		        $result = $conn->query($sql);
		        
		        while($row =mysqli_fetch_assoc($result)) {
		        	
		          	$data[] = $row;
		        }
		        
		        if(count($data) == 0 ){
		        	if($languageCode == "hi"){

	              		$message = "क्षमा करें हमें इस अकाऊँट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकता हूं?";
	            	}else{
	            		$message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
	            	}
		        }else{
		        	// Create Session Entry in DB 
		        	$sql = "INSERT INTO session_data(sessionId,account_number,mobile_number) VALUES ('$sessionId','$account_number','$mobile_number')";
            		$result = $conn->query($sql);
            		$otp = rand(1000,9999);

            		// Send OTP SMS on Mobile Number
            		$curl = curl_init();
              		$url = "http://2factor.in/API/V1/e46e0ef4-5d1b-11ea-9fa5-0200cd936042/SMS/".$mobile_number."/".$otp;
              		curl_setopt_array($curl, array(
                		CURLOPT_URL => $url,
                		CURLOPT_RETURNTRANSFER => true,
               	 		CURLOPT_ENCODING => "",
                		CURLOPT_MAXREDIRS => 10,
                		CURLOPT_TIMEOUT => 0,
                		CURLOPT_FOLLOWLOCATION => true,
                		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                		CURLOPT_CUSTOMREQUEST => "POST",
              		));

              		$response = curl_exec($curl);
              		$err = curl_error($curl);

              		curl_close($curl);

              		// Delete Previous OTP
          			$deleteSql = "DELETE FROM validate_otp WHERE mobile_number ='$mobile_number'";
          			$result     = $conn->query($deleteSql);

              		// Insert OTP for validation Purpose
              		$sql = "INSERT INTO validate_otp(mobile_number,otp) VALUES ('$mobile_number','$otp')";
              		$result = $conn->query($sql);

              		if($languageCode == 'hi'){
              			$message = "हमने आपके मोबाइल नंबर पर एक OTP भेजा है। कृपया मुझे ओटीपी प्रदान करें।";
	            	}else{
	            		$message = "We have sent an OTP to your mobile number. Please provide me the OTP.";
	            	}
		        }
      		}else{
      			$message = "Something went wrong";
      		}
  		}else if($intent == "authenticationselection - custom - yes" || $intent == "reenterotp"){

  			$otp = $requestDecode->queryResult->parameters->OTP;
  			
  			if($isSessionAvailable == "Yes" || $isSessionAvailable == "NotValidate"){
  				$mobile_number = $aUserData['mobile_number'];
  				// Check OTP is Valid Or Not
        		$sql = "SELECT * FROM validate_otp WHERE $mobile_number ='$mobile_number' AND otp='$otp' ORDER BY validate_otp_id DESC LIMIT 1";
        		$result     = $conn->query($sql);
        		$row  = mysqli_fetch_assoc($result);

        		if(count($row) > 0 && $row != ""){
        			// Update Validation flag as 1
        			// 0 Means Not Validate and 1 is for validate
        			$updatevalidationFlag = "UPDATE session_data SET is_validate='1' WHERE sessionId='$sessionId'";
        			
        			$updatevalidationFlagresult = $conn->query($updatevalidationFlag);

        			// Delete All OTP
          			$deleteSql = "DELETE FROM validate_otp WHERE mobile_number ='$mobile_number'";
          			$result     = $conn->query($deleteSql);
        			// If Valid OTP
        			if($languageCode != "hi"){
		        		$message = "You are successfully authenticated now. You can now ask me regarding your account details";
		        	}else{
		        		$message = "अब आप सफलतापूर्वक प्रमाणित हो गए हैं। अब आप मुझसे अपने खाते के बारे में पूछ सकते हैं ";
		        	}

        		}else{
        			// If OTP is wrong then reenter OTP
        			$data['followupEventInput']['name'] = "recallotp";
			        $data['followupEventInput']['parameters']['OTP'] = '';
			        $data['languageCode'] = "en-US";
			        $aBlankDetails = json_encode($data);
			        echo $aBlankDetails;exit;
        		}
  			}else if($isSessionAvailable == "No"){
  				$data['followupEventInput']['name'] = "recall";
          		$data['followupEventInput']['parameters']['Account_Number'] = '';
          		$data['followupEventInput']['parameters']['Contact'] = '';
          		$data['languageCode'] = "en-US";
          		$aBlankDetails = json_encode($data);
          		echo $aBlankDetails;exit;
  			}else{
  				// If OTP is wrong then reenter OTP
    			$data['followupEventInput']['name'] = "recallotp";
		        $data['followupEventInput']['parameters']['OTP'] = '';
		        $data['languageCode'] = "en-US";
		        $aBlankDetails = json_encode($data);
		        echo $aBlankDetails;exit;
  			}
  		}else if($intent == "Greeting"){

  			// Greeting
  			$current_time = date("h:i a");
  			$current_time = strtotime($current_time);
	        if($current_time > strtotime("11:59 am") && $current_time < strtotime("3:59 pm")){
	          $greeting = "Good Afternoon! ";
	        }else if($current_time > strtotime("3:59 pm") && $current_time < strtotime("11:59 pm")){
	          $greeting = "Good Evening! ";
	        }else{
	          $greeting = "Good Morning! ";
	        }


  			if($isSessionAvailable == "Yes" || $isSessionAvailable == "NotValidate"){
  				$sessionId = $sessionId;
	            $sql = "DELETE FROM session_data WHERE sessionId = '$sessionId'";
	           
	            $result     = $conn->query($sql);
		        if($languageCode == 'hi'){
		        	$message = $greeting . " हाय मैं Conneqt बैंक मित्र हूं। Conneqt बैंक में आपका स्वागत है! क्या आप आगे बढ़ना चाहोगे";
	          	}else{
	          		$message = $greeting." Hi I am Conneqt bank buddy.  Welcome to Conneqt bank!. I can interact in English and Hindi, which language would you be more comfortable with.";
	          	}
  			}else if($isSessionAvailable == "No"){
  				if($languageCode == 'hi'){
  					$message = $greeting . " हाय मैं Conneqt बैंक मित्र हूं। Conneqt बैंक में आपका स्वागत है! क्या आप आगे बढ़ना चाहोगे";
	  	    	}else{
	  	    		$message = $greeting." Hi I am Conneqt bank buddy.  Welcome to Conneqt bank!. I can interact in English and Hindi, which language would you be more comfortable with.";
	  	    	}
  			}
  		}else if($intent == "BalanceRequest - yes"){
  			if($isSessionAvailable == "Yes"){
  				$account_number = $aUserData['account_number'];
        		$mobile_number 	= $aUserData['mobile_number'];
        		// Get Account Balance
        		$sql = "SELECT CONCAT(vcd.firstname,' ',vcd.lastname) AS name , vcscf.cf_864 as account_balance FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856='$account_number' AND vcd.mobile='$mobile_number' ORDER BY vcd.contactid DESC";
        		$result = $conn->query($sql);
		        while($row =mysqli_fetch_assoc($result)) {
		          $data[] = $row;
		        }

		        if(count($data) == 0){
		          	if($languageCode == "hi"){
		          		$message = "क्षमा करें हमें इस अकाऊँट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकता हूं?";
		        	}else{
		        		$message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
		        	}
		        }else{
		        	if($languageCode == "hi"){
		        		$message = "Dear ".$data[0]['name'] . ",आपका अकाऊँट बैलेस ".$data[0]['account_balance']. " . मैं आपकी क्या मदद कर सकता हूं?";
		          	}else{
		          		$message = "Dear ".$data[0]['name'] . ", your account balance is ".$data[0]['account_balance']. " .  What else I can help you with?";
		          	}
		        }
  			}else if($isSessionAvailable == "No"){
  				$data['followupEventInput']['name'] = "recall";
          		$data['followupEventInput']['parameters']['Account_Number'] = '';
          		$data['followupEventInput']['parameters']['Contact'] = '';
          		$data['languageCode'] = "en-US";
          		$aBlankDetails = json_encode($data);
          		echo $aBlankDetails;exit;
  			}else{
  				// If OTP is wrong then reenter OTP
    			$data['followupEventInput']['name'] = "recallotp";
		        $data['followupEventInput']['parameters']['OTP'] = '';
		        $data['languageCode'] = "en-US";
		        $aBlankDetails = json_encode($data);
		        echo $aBlankDetails;exit;
  			}
  		}else if($intent == "HomeLoan"){
          	if($accountAndMobileNumberExist == "No"){
            	if($languageCode == "hi"){
          			$message = "क्षमा करें हमें इस अकाऊँट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकता हूं?";
        		}else{
        			$message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
        		}
          	}else{
          		$home_loan_amount = $requestDecode -> queryResult -> parameters -> HomeLoanAmount;
            	// Update Home Loan Amount
            	$home_loan_amount = str_replace(' ', '', $home_loan_amount);
            	$sql = "UPDATE vtiger_contactscf SET cf_860='$home_loan_amount' WHERE cf_856= $account_number";
            	$result = $conn->query($sql);
            	$message = "Thank you for the details! I have passed on the details to our team, and one of our representative would reach out to you shortly to help you out. What else I can help you with?";
          	}
  		}else if($intent == "openFDaccount"){
	        if($accountAndMobileNumberExist == "No"){
            	if($languageCode == "hi"){
          			$message = "क्षमा करें हमें इस अकाऊँट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकता हूं?";
        		}else{
        			$message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
        		}
          	}else{
          		$fd_amount        	= $requestDecode -> queryResult -> parameters -> FDAmount;
	        	$locking_period   	= $requestDecode -> queryResult -> parameters -> LockingPeriod;
          		// Update FD Amount
	            $fd_amount = str_replace(' ', '', $fd_amount);
	            $locking_period = str_replace(' ', '', $locking_period);
	            $sql = "UPDATE vtiger_contactscf SET cf_866='$fd_amount' , cf_868='$locking_period' WHERE cf_856= $account_number";
	            $result = $conn->query($sql);
	            $message = "Thank you for the details! I have passed on the details to our team, and one of our representative would reach out to you shortly to help you out with the various Fixed Deposit rates and options. What else I can help you with?";
	            
          	}
  		}else if($intent == "TicketDetails"){
		        if($accountAndMobileNumberExist == "No"){
		          if($languageCode == "hi"){
		          		$message = "क्षमा करें हमें इस अकाऊँट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकता हूं?";
		        	}else{
		        		$message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
		        	}
		          
		        }else{
		          $ticket_number   = $requestDecode->queryResult->parameters->TicketNumber;

		          if($ticket_number != "" && $mobile_number != ""){
		            $sql = "SELECT CONCAT(vcd.firstname,' ',vcd.lastname) AS name,vtt.status FROM vtiger_troubletickets vtt JOIN vtiger_crmentity vce ON vtt.ticketid = vce.crmid JOIN vtiger_contactdetails vcd ON vtt.contact_id = vcd.contactid WHERE vce.deleted='0' AND vcd.mobile='$mobile_number' AND vtt.ticketid='$ticket_number' ORDER BY vtt.ticketid DESC";
		            $data = array();
		            $result = $conn->query($sql);
		            while($row =mysqli_fetch_assoc($result)) {
		              	$data[] = $row;
		            }
		            
		            if(count($data) == 0){
		            	$message = "Sorry we could not find any details against this Ticket number and Mobile number. What else I can help you with?";
		              	
		            }else{
		              
		              	$message = "Dear ". $data[0]['name'] .",current status of your ticket ".$ticket_number ." is ". $data[0]['status'].". What else I can help you with?";
		              	
		            }
		          }
		        }
  			
  		}
  	}else{
  		$message = "Something went wrong";
  	}

  	
  	// Send Respose to Dialogflow fulfillment
  	$data = array (
    	'fulfillmentText' => $message
  	);
  	$aFinalDialogflowResponse = json_encode($data);
  	echo $aFinalDialogflowResponse;
  	$conn -> close();
?>
