<?php 
        //Data, connection, auth
        //$dataFromTheForm = $_POST['fieldName']; // request data from the form
        $soapUrl = "https://sms.ooredoo.com.om/User/bulkpush.asmx?wsdl"; // asmx URL of WSDL
        $soapUser = "THIQAH";  //  username
        $soapPassword = "MUSCAT@9984"; // password

        // xml post structure

        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
xmlns:web="https://web.nawras.com.om/">
 <soapenv:Header/>
 <soapenv:Body>
 <web:SendSMS>
 <web:UserName>THIQAH</web:UserName>
 <web:Password>MUSCAT@9984</web:Password>
 <web:Message>Welcome to Nawras Bulk SMS</web:Message>
 <web:Priority>1</web:Priority>
 <web:Schdate>11/11/2012</web:Schdate>
 <web:Sender>Nawras</web:Sender>
 <web:AppID>2000</web:AppID>
 <web:SourceRef>12</web:SourceRef>
 <web:MSISDNs>99369401</web:MSISDNs>
 </web:SendSMS>
 </soapenv:Body>
</soapenv:Envelope>';   // data from the form, e.g. some ID number

           $headers = array(
                        "Content-type: text/xml;charset=\"utf-8\"",
                        "Accept: text/xml",
                        "Cache-Control: no-cache",
                        "Pragma: no-cache",
                        "SOAPAction: http://connecting.website.com/WSDL_Service/GetPrice", 
                        "Content-length: ".strlen($xml_post_string),
                    ); //SOAPAction: your op URL

            $url = $soapUrl;

            // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // converting
            $response = curl_exec($ch); 
            if (curl_errno($ch)) {
    $error_msg = curl_error($ch);
    echo $error_msg;
}
            curl_close($ch);
            echo $response;
            // converting
            $response1 = str_replace("<soap:Body>","",$response);
            $response2 = str_replace("</soap:Body>","",$response1);

            // convertingc to XML
            $parser = simplexml_load_string($response2);
            // user $parser to get your data out of XML response and to display it.
            echo $parser;
    ?>
