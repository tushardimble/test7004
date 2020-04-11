
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
  	$username 	= "axisbankcrm3";
  	$password 	= "axisbankcrm3";
  	$dbname 	= "axisbankcrm3";
  	$data 		= array();
  	// Connect with Db 
  	$conn = new mysqli($servername, $username, $password, $dbname);

  	// Check connection
  	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
  	}


  	//  Make Second DB Connection for logger
  	$logservername = "66.45.232.178";
  	$logusername 	= "logger";
  	$logpassword 	= "logger";
  	$logdbname 	= "logger";
  	// Connect with Db 
  	$logconn = new mysqli($logservername, $logusername, $logpassword, $logdbname);

  	// Check connection
  	if ($logconn->connect_error) {
    	die("Connection failed: " . $logconn->connect_error);
  	}



  	// Get Json Input from Dialogflow
  	$request 		= file_get_contents('php://input');
  	$requestDecode 	= json_decode($request);
  	rand(1000,10000);
  	$_SESSION["digit"] = rand(1000,10000);;

  	$intent 		= $requestDecode	->	queryResult	->	intent 	-> 	displayName;
  	$languageCode 	= $requestDecode 	-> 	queryResult -> 	languageCode;
  	$userQueryText 	= $requestDecode 	-> 	queryResult -> 	queryText;
  	$log_current_time = date("Y-m-d H:i:s");
  	// Get Session Id
  	$outputContexts 		= 	$requestDecode -> queryResult -> outputContexts[0] -> name;
  	$outputContextsArray 	= 	explode("/", $outputContexts);
  	$sessionId 				= 	$outputContextsArray[4];
  	$_SESSION['dialog'] = $sessionId;
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
  		if($intent == "HomeLoan" || $intent == "openFDaccount" || $intent == "TicketDetails" || $intent == "createTicket"){

  			if($isSessionAvailable == "Yes"){
  				$account_number = $aUserData['account_number'];
        		$mobile_number = $aUserData['mobile_number'];
	  			$sql = "SELECT CONCAT(vcd.firstname,' ',vcd.lastname) AS name ,vcd.contactid, vcscf.cf_864 as account_balance FROM vtiger_contactdetails vcd JOIN vtiger_crmentity vce ON vcd.contactid=vce.crmid JOIN vtiger_contactscf vcscf ON vcd.contactid=vcscf.contactid WHERE vce.deleted=0 AND vcscf.cf_856='$account_number' AND vcd.mobile='$mobile_number' ORDER BY vcd.contactid DESC";
	        	$result = $conn->query($sql);
		        while($row =mysqli_fetch_assoc($result)) {
		          $data[] = $row;
		        }

		        if(count($data) == 0){
		        	$accountAndMobileNumberExist = "No";
		        }else{
		        	$iContactid = $data[0]['contactid'];
		        	$sContactname = $data[0]['name'];
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

	              		$message = "क्षमा करें हमें इस अकाऊँट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकती  हूं?";
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
              			$message = "हमने आपके मोबाइल नंबर पर एक ओटीपी भेजा है। कृपया मुझे ओटीपी नंबर बताये|";
	            	}else{
	            		$message = "We have sent an OTP to your mobile number. Please provide me the OTP.";
	            	}
		        }
      		}else{
      			$message = "Something went wrong";
      		}

  		}else if($intent == "authenticationselection - custom - yes" || $intent == "reenterotp"){

  			$otp = $requestDecode->queryResult->parameters->OTP;
  			$otp = str_replace(' ', '', $otp);
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
	        	if($languageCode == 'hi'){
	          		$greeting = "नमस्कार! ";
	          	}else{
	          		$greeting = "Good Afternoon! ";
	          	}
	        }else if($current_time > strtotime("3:59 pm") && $current_time < strtotime("11:59 pm")){
	        	if($languageCode == 'hi'){
	          		$greeting = "शुभ संध्या";
	          	}else{
	          		$greeting = "Good Evening! ";
	          	}
	          
	        }else{
	        	if($languageCode == 'hi'){
	          		$greeting = "शुभ प्रभात! ";
	          	}else{
	          		$greeting = "Good Morning! ";
	          	}
	          
	        }

  			if($isSessionAvailable == "Yes" || $isSessionAvailable == "NotValidate"){
  				$sessionId = $sessionId;
	        	$sql = "DELETE FROM session_data WHERE sessionId = '$sessionId'";
	           
	        	$result     = $conn->query($sql);
	      		if($languageCode == 'hi'){
	        		$message = $greeting . " हाय मैं कोन्नेक्त  बैंक मित्र हूं। कोन्नेक्त  बैंक में आपका स्वागत है!  मैं आपको हमारे बँक अकाऊंट संबंधी और सामान्य प्रश्नों में मदत कर सकती हूँ.";
        		}else{
          				//$message = $greeting." Hi I am Conneqt bank buddy.  Welcome to Conneqt bank!. I can interact in English and Hindi, which language would you be more comfortable with.";
          			$message = $greeting." Hi I am Conneqt bank buddy.  Welcome to Conneqt bank!.I can help you with bank account related or general queries about our products and service.";
        		}


  			}else if($isSessionAvailable == "No"){

  				if($languageCode == 'hi'){
  					$message = $greeting . " हाय मैं कोन्नेक्त  बैंक मित्र हूं। कोन्नेक्त  बैंक में आपका स्वागत है!  मैं आपको हमारे बँक अकाऊंट संबंधी और सामान्य प्रश्नों में मदत कर सकती हूँ.";
	  	    	}else{
	  	    		$message = $greeting." Hi I am Conneqt bank buddy.  Welcome to Conneqt bank!.I can help you with bank account related or general queries about our products and service.";
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
		          		$message = "क्षमा करें हमें इस अकाऊँट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकती  हूं?";
		        	}else{
		        		$message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
		        	}
		        }else{
		        	if($languageCode == "hi"){
		        		$message = "Dear ".$data[0]['name'] . ",आपका अकाऊँट बैलेस ".$data[0]['account_balance']. " है. मैं आपकी क्या मदद कर सकती  हूं?";
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
          			$message = "क्षमा करें हमें इस अकाऊँट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकती  हूं?";
        		}else{
        			$message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
        		}


          	}else{
          		$home_loan_amount = $requestDecode -> queryResult -> parameters -> HomeLoanAmount;
            	// Update Home Loan Amount
            	$home_loan_amount = str_replace(' ', '', $home_loan_amount);
            	$sql = "UPDATE vtiger_contactscf SET cf_860='$home_loan_amount' WHERE cf_856= $account_number";
            	$result = $conn->query($sql);
            	if($languageCode == "hi"){
            		$message = "डिटेल देने के लिए धन्यवाद ! मैंने अपनी टीम को डिटेल  दे दिया है, और हमारा एक प्रतिनिधि शीघ्र ही आपकी मदद करने के लिए आपके पास पहुंच जाएगा। मैं आपकी और क्या मदद कर सकती  हूं?";
            	}else{
            		$message = "Thank you for the details! I have passed on the details to our team, and one of our representative would reach out to you shortly to help you out. What else I can help you with?";
            	}
            	
          	}

  		}else if($intent == "openFDaccount"){
	        if($accountAndMobileNumberExist == "No"){
            	if($languageCode == "hi"){
          			$message = "क्षमा करें हमें इस अकाऊँट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकती  हूं?";
        		}else{
        			$message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
        		}
          	}else{
          		$fd_amount        	= $requestDecode -> queryResult -> parameters -> FDAmount;
	        	$locking_period   	= $requestDecode -> queryResult -> parameters -> LockingPeriod;
	        	if($fd_amount != "" && $locking_period != ""){
	          		// Update FD Amount
		            $fd_amount = str_replace(' ', '', $fd_amount);
		            $locking_period = str_replace(' ', '', $locking_period);
		            $sql = "UPDATE vtiger_contactscf SET cf_866='$fd_amount' , cf_868='$locking_period' WHERE cf_856= $account_number";
		            $result = $conn->query($sql);
		            if($languageCode == "hi"){
	            		$message = "डिटेल देने के लिए धन्यवाद ! मैंने अपनी टीम को डिटेल  दे दिया है, और हमारा एक प्रतिनिधि शीघ्र ही आपकी मदद करने के लिए आपके पास पहुंच जाएगा। मैं आपकी और क्या मदद कर सकती  हूं?";
	            	}else{
	            		$message = "Thank you for the details! I have passed on the details to our team, and one of our representative would reach out to you shortly to help you out. What else I can help you with?";
	            	}
	            }else{
	            	$data['followupEventInput']['name'] = "fdreenter";
	          		$data['followupEventInput']['parameters']['FDAmount'] = '';
	          		$data['followupEventInput']['parameters']['LockingPeriod'] = '';
	          		$data['languageCode'] = "en-US";
	          		$aBlankDetails = json_encode($data);
	          		echo $aBlankDetails;exit;
	            }
          	}
          	
  		}else if($intent == "TicketDetails"){
		        if($accountAndMobileNumberExist == "No"){
		          	if($languageCode == "hi"){
		          		$message = "क्षमा करें हमें इस अकाऊँट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकती  हूं?";
		        	}else{
		        		$message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
		        	}
		          
		        }else{
		          $ticket_number   = $requestDecode->queryResult->parameters->TicketNumber;
		          //$ticket_number = explode(" ",$ticket_number);
		          //$ticket_number = $ticket_number[1];

		          if($ticket_number != "" && $mobile_number != ""){
		            $sql = "SELECT CONCAT(vcd.firstname,' ',vcd.lastname) AS name,vtt.status FROM vtiger_troubletickets vtt JOIN vtiger_crmentity vce ON vtt.ticketid = vce.crmid JOIN vtiger_contactdetails vcd ON vtt.contact_id = vcd.contactid WHERE vce.deleted='0' AND vcd.mobile='$mobile_number' AND vtt.ticketid='$ticket_number' ORDER BY vtt.ticketid DESC";

		            $data = array();
		            $result = $conn->query($sql);
		            while($row =mysqli_fetch_assoc($result)) {
		              	$data[] = $row;
		            }
		           
		            if(count($data) == 0){
		            	if($languageCode == "hi"){
			          		$message = "क्षमा करें हमें इस टिकट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकती  हूं?";
			        	}else{
			        		$message = "Sorry we could not find any details against this Ticket number and Mobile number. What else I can help you with?";
			        	}
		            	
		            }else{
		              	if($languageCode == "hi"){
			          		$message = "Dear ". $data[0]['name'] .",आपके टिकट ".$ticket_number ." का स्टेटस  ". $data[0]['status']." है. मैं आपकी क्या मदद कर सकती  हूं?";
			        	}else{
			        		$message = "Dear ". $data[0]['name'] .",current status of your ticket ".$ticket_number ." is ". $data[0]['status'].". What else I can help you with?";
			        	}
		            }
		          }
		        }
  			
  		}else if($intent == "createTicket"){
  			if($accountAndMobileNumberExist == "No"){
            	if($languageCode == "hi"){
          			$message = "क्षमा करें हमें इस अकाऊँट नंबर और मोबाइल नंबर के खिलाफ कोई विवरण नहीं मिला। मैं आपकी क्या मदद कर सकती  हूं?";
        		}else{
        			$message = "Sorry we could not find any details against this account number and mobile number. What else I can help you with?";
        		}
          	}else{

          		$ticket_desc =  $requestDecode->queryResult->parameters->ticket_desc;
	  			//Generate Ticket
				$sqlCEST 	= "SELECT id FROM vtiger_crmentity_seq";

				$resultCEST = $conn->query($sqlCEST);
	  			$aVCrmSeq  = mysqli_fetch_assoc($resultCEST);
	  			$cesT = $aVCrmSeq['id']+1;

	  			$sqlCESUT = "UPDATE vtiger_crmentity_seq SET id=$cesT";
	  			$resCESUT = $conn->query($sqlCESUT);

	  			$sHelpDesk = "HelpDesk";
	  			$sqlLST = "SELECT prefix,cur_id FROM vtiger_modentity_num WHERE semodule='$sHelpDesk'";
	  			//echo $sqlLST;exit;
	  			$resLST = $conn->query($sqlLST);
	  			$detLST = mysqli_fetch_assoc($resLST);
	  			

	  			$ticketNo = $detLST['prefix'].$detLST['cur_id'];
	  			$detLST['cur_id'] = $detLST['cur_id']+1;
	  			$prefix = $detLST['prefix'];
	  			$iCur_id = $detLST['cur_id'];
	  			$sqlLSUT = "UPDATE vtiger_modentity_num SET cur_id=$iCur_id WHERE semodule='HelpDesk' AND prefix='$prefix'";
	  			
	  			$resLSUT = $conn->query($sqlLSUT);

	  			$current_user_id = $iContactid;
	  			$sqlCUFT = "INSERT INTO vtiger_crmentity_user_field (recordid,userid,starred) VALUES ($cesT,$current_user_id,0)";
	  			
	  			$resCUFT = $conn->query($sqlCUFT);
	  			$created_date_time = date('Y-m-d H:i:s');
	  			$sqlCET = "INSERT INTO vtiger_crmentity(crmid, smcreatorid, smownerid, modifiedby, setype, createdtime, modifiedtime,presence,source, label) VALUES ($cesT,$current_user_id,$current_user_id,$current_user_id,'HelpDesk','$created_date_time','$created_date_time',1,'CRM','$ticketNo')";
	  			//echo $sqlCET;exit;
	  			$resCET = $conn->query($sqlCET);

	  			$sqlTT = "INSERT INTO vtiger_troubletickets(ticketid,ticket_no,priority,title,status,contact_id) VALUES($cesT,'$ticketNo','Normal','$ticket_desc','Open',$current_user_id)";
	  			
	  			$resTT = $conn->query($sqlTT);

	  			$sqlTCF = "INSERT INTO vtiger_ticketcf SET ticketid=$cesT";
				$resTCF = $conn->query($sqlTCF);
				$conn -> close();
				if($languageCode == "hi"){
					$message = "प्रिय ". $sContactname .", आपका टिकट TK ". $cesT ." सफलतापूर्वक बनाया गया है। हम जल्द से जल्द टिकट को हल करने की कोशिश करेंगे और एसएमएस के माध्यम से आपके टिकट पर अपडेट भेजेंगे। आप फिर से टिकट की स्थिति भी देख सकते हैं।";
				}else{
					
					$message = "Dear ". $sContactname .", your ticket TK ". $cesT ." is successfully created. We will try to resolve the ticket as soon as possible and will send updates on your ticket via SMS. You can also again come and check the status of the ticket.";
				}
				
			}
		
  		}else if($intent == "OPERATOR_REQUEST"){
        $user_name   = $requestDecode->queryResult->parameters->user_name;
        $message = "Dear , " .$user_name. " ,I'll hand you over to a live operator. Dear User you are successfully connected with our agent.";
      }
  	}else{
  		$message = "Something went wrong";
  	}
 	 
  	// Check Session Exist in Our Db
  	if($sessionId != ""){
  		$sCheckSessionSql 	= "SELECT * FROM tx_chat_summary WHERE session_id = '$sessionId'";
  		$sSessionResult 	= $logconn -> query($sCheckSessionSql);
  		$aSessionData 		= mysqli_fetch_assoc($sSessionResult);
  		//echo"<pre>";print_r($aSessionData);exit;
  		if(count($aSessionData) == 0){
  			// Insert tx_chat_summary
    		$sLogInsertSQL = "INSERT INTO tx_chat_summary(session_id,chat_start_time,chat_end_time,contact_id,last_agent_id) VALUES ('$sessionId','$log_current_time','$log_current_time','0','0')";
    		$sLogResult = $logconn -> query($sLogInsertSQL);
    		$chat_summary_id = $logconn->insert_id;
  		}else{
  			// Update Chat end time
    		$sLogInsertSQL = "UPDATE tx_chat_summary SET chat_end_time = '$log_current_time' WHERE session_id='$sessionId'";
    		$sLogResult = $logconn -> query($sLogInsertSQL);
    		$chat_summary_id = $aSessionData['summary_id'];
  		}

  		
  		mysqli_set_charset($logconn,'utf8');
	   	// Insert Log In DB User Query(Customer Query)
	    $sLogInsertCustSQL = "INSERT INTO tx_chat_session_details(chat_summary_id,message,msg_from,agent_id,created_at) VALUES ('$chat_summary_id','$userQueryText','customer','0','$log_current_time')";
	    // echo $sLogInsertCustSQL;exit;
	    $sLogBotResult = $logconn -> query($sLogInsertCustSQL);

	    if($message == ""){
	    	$message = $requestDecode 	-> 	queryResult -> 	fulfillmentText;
        $message1 = preg_replace('/[^A-Za-z0-9\-]/', ' ', $message);
        $message = $message;
        //echo $message;exit;
	   	}else{
        $message1 = $message;
      }

	    // Insert Log In DB User Query(Bot Answer)
	    $sLogInsertBotSQL = "INSERT INTO tx_chat_session_details(chat_summary_id,message,msg_from,agent_id,created_at) VALUES ('$chat_summary_id','$message1','bot','0','$log_current_time')";
      //echo $sLogInsertBotSQL;exit;
	    $sLogBotResult = $logconn -> query($sLogInsertBotSQL);

	    // Update conversation time 
	    // Update Chat end time
    	$sLogUpdateSQL = "UPDATE tx_chat_summary SET chat_end_time = '$log_current_time' WHERE session_id='$sessionId'";
    	$sLogResult = $logconn -> query($sLogUpdateSQL);

  		$logconn -> query($a);
  	}
    
  	$conn -> close();
  	// Send Respose to Dialogflow fulfillment
  	
  	if($intent=="debit card"){
  		$fulfillmentMessages[0]['platform']="ACTIONS_ON_GOOGLE";
  	$fulfillmentMessages[0]['linkOutSuggestion']['destinationName']="test";
  	$fulfillmentMessages[0]['linkOutSuggestion']['uri']="https://audiodemo-ftnluv.web.app/";
  	}
  	$data = array (
    	'fulfillmentText' => $fulfillmentMessages
  	);
// $aFinalDialogflowResponse = "fulfillmentMessages": [
//       {
//         "platform": "ACTIONS_ON_GOOGLE",
//         "linkOutSuggestion": {
//           "destinationName": "test",
//           "uri": "https://audiodemo-ftnluv.web.app/"
//         }
//       }
//     ];
  	$aFinalDialogflowResponse = json_encode($data,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

  	echo $aFinalDialogflowResponse;
  	
?>
