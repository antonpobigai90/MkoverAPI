<?php
	
	echo "test";
	
	$result = push("793c35761fd2e2b7825b223cf7beae57cdf1798e1327454b9fc0972dd6748726", array("aps" => array("alert" => "Mkover push test")));
//	c853db77dbcf380675b8c45cda34cd9ce0588a44a11af2be5592ee9684054e01
//254473322e1159b2f897520a3e3dc7555a67f71a6e6e5ae01900a42e73d8ddeb
	if (!$result)
		echo 'Message not delivered' . PHP_EOL;
	else
		echo 'Message successfully delivered' . PHP_EOL;
		
	
	function push($deviceToken, $body = array()) {
	    
		$deviceToken = strtolower(str_replace(array(" ", "-", "_"), array("", "", ""), $deviceToken));

		// Put your private key's passphrase here:
		$passphrase = '123456';

		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', 'ckdist.pem');

		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		stream_context_set_option($ctx, 'ssl', 'cafile', 'entrust_2048_ca.cer');


		// Open a connection to the APNS server
		//gateway.push.apple.com
	//	$fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

		$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
		
		if (!$fp) {
			return false;
		}



		// Encode the payload as JSON
		$payload = json_encode($body);

		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));

		if (!$result)
			return false;

		// Close the connection to the server
		fclose($fp);
		return true;
	}
	

?>