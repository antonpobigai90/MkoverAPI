<?php

class DB {

    private $pdo = NULL;
    // For Android Notification
    private $android_GCM = NULL;
    // For Iphone Notification
    private $sandBox = 0;
    private $pem_Dev = '/var/www/html/bookmwah/webservices/Test_Dev.pem';
    private $pem_Pro = '/var/www/html/bookmwah/webservices/Test_Pro.pem';
    private $passPhrase = '12345';
    // For BASE URL
    public $baseurl = NULL;

    // Creation of Database Connection
    function __construct() {
        $host = 'localhost';
        $db = 'majedgar_mkover';
        $user = 'majedgar_mkover';
        $pass = 'majedgar';
        $charset = 'utf8';

        try {
            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $this->pdo = new PDO($dsn, $user, $pass);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

        $this->baseurl = $this->curPageURL();

        define("UPLOADS", "/uploads");
    }

    function curPageURL() {
        $pageURL = 'http';
        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"];
        }
        return $pageURL;
    }

    // Get the records of any query
    function query_result($query) {
        $result = array();
        $run = $this->pdo->query($query);
        while ($row = $run->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        return $result;
    }

    // Get Data from a particular table
    function get_all_records($table) {
        $result = array();
        $query = "SELECT * FROM `$table`";
        $run = $this->pdo->query($query);
        while ($row = $run->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }
        return $result;
    }

    // Get Data from a table can also apply different conditions
    function get_record_where($table, $where, $column = '', $group_by = '', $order_by = '', $order_by_type = '', $limit = '') {
        $result = array();
        $query = "SELECT ";
        if ($column != '')
            $query .= "$column ";
        else
            $query .= "* ";
        $query .= "FROM $table WHERE ";

        if (is_array($where)) {
            $i = 0;
            foreach ($where as $key => $value) {
                $key_ar = explode(' ', $key);
                $key = trim($key_ar[0]);

                $condition = '=';
                if (!empty($key_ar[1]))
                    $condition = trim($key_ar[1]);

                if ($i == 0)
                    $query .= "$key $condition :$key ";
                else
                    $query .= "AND $key $condition :$key ";

                $where_array[":$key"] = $value;

                $i++;
            }

            if ($group_by != '')
                $query .= "GROUP BY $group_by ";

            if ($order_by != '') {
                $query .= "ORDER BY $order_by ";
                if ($order_by_type != '')
                    $query .= "$order_by_type ";
            }

            if ($limit != '')
                $query .= "LIMIT $limit";

            $run = $this->pdo->prepare($query);
            $run->execute($where_array);
            while ($row = $run->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $row;
            }
        }
        return $result;
    }

    // For Inserting records in database
    function insert_records($table, $data) {
        $insert_id = 0;
        $query = "INSERT INTO $table";
        if (is_array($data)) {
            $statement_1 = "(";
            $statement_2 = "VALUES(";
            foreach ($data as $key => $value) {
                $statement_1 .= "$key,";
                $statement_2 .= ":$key,";

                $insert_data[":$key"] = $value;
            }
            $statement_1 = rtrim($statement_1, ",");
            $statement_2 = rtrim($statement_2, ",");

            $statement_1 .= ") ";
            $statement_2 .= ")";

            $query .= $statement_1 . $statement_2;
          //  echo $query;

            $insert = $this->pdo->prepare($query);
            $insert->execute($insert_data);
	//    print_r($insert_data);
            $insert_id = $this->pdo->lastInsertId();
        }
        return $insert_id;
    }

    // For updation of database records
    function update_records($table, $data, $where) {
        $affected_rows = 0;
        $query = "UPDATE $table SET ";
        if (is_array($data) && is_array($where)) {
            foreach ($data as $key => $value) {
                $query .= "$key = :$key, ";
                $exe_array[":$key"] = $value;
            }

            $query = rtrim($query, ", ");

            $query .= " WHERE ";

            $i = 0;
            foreach ($where as $key => $value) {
                $key_ar = explode(' ', $key);
                $key = trim($key_ar[0]);

                $condition = '=';
                if (!empty($key_ar[1]))
                    $condition = trim($key_ar[1]);

                if ($i == 0)
                    $query .= "$key $condition :$key ";
                else
                    $query .= "AND $key $condition :$key ";

                $exe_array[":$key"] = $value;

                $i++;
            }

            $run = $this->pdo->prepare($query);
            $run->execute($exe_array);
            $affected_rows = $run->rowCount();
        }
        return $affected_rows;
    }

    // For deleting any records from database
    function delete_record($table, $where) {
        $affected_rows = 0;
        $query = "DELETE FROM $table WHERE ";
        if (is_array($where)) {
            $i = 0;
            foreach ($where as $key => $value) {
                $key_ar = explode(' ', $key);
                $key = trim($key_ar[0]);

                $condition = '=';
                if (!empty($key_ar[1]))
                    $condition = trim($key_ar[1]);

                if ($i == 0)
                    $query .= "$key $condition :$key ";
                else
                    $query .= "AND $key $condition :$key ";

                $exe_array[":$key"] = $value;

                $i++;
            }

            $run = $this->pdo->prepare($query);
            $run->execute($exe_array);
            $affected_rows = $run->rowCount();
        }
        return $affected_rows;
    }

    // For get records using JOIN query
    function get_record_join($tables, $keys_1, $keys_2, $join_type, $where = '', $column = '', $group_by = '', $order_by = '', $order_by_type = '', $limit = '') {
        $records = array();
        if (is_array($tables) && is_array($keys_1) && is_array($keys_2) && is_array($join_type)) {
            $query = "SELECT ";
            if ($column != '')
                $query .= "$column ";
            else
                $query .= "* ";

            $query .= "FROM " . $tables[0] . " ";

            foreach ($tables as $key => $value) {
                if ($key != 0)
                    $query .= strtoupper($join_type[$key]) . " JOIN $value ON " . $keys_1[$key - 1] . " = " . $keys_2[$key - 1] . " ";
            }
        }

        $where_array = array();

        if ($where != '' && is_array($where)) {
            $query .= "WHERE ";
            $i = 0;
            foreach ($where as $key => $value) {
                $key_ar = explode(' ', $key);
                $key = trim($key_ar[0]);

                $condition = '=';
                if (!empty($key_ar[1]))
                    $condition = trim($key_ar[1]);

                if ($i == 0)
                    $query .= "$key $condition :$key ";
                else
                    $query .= "AND $key $condition :$key ";

                $where_array[":$key"] = $value;

                $i++;
            }
        }

        if ($group_by != '')
            $query .= "GROUP BY $group_by ";

        if ($order_by != '') {
            $query .= "ORDER BY $order_by ";
            if ($order_by_type != '')
                $query .= "$order_by_type ";
        }

        if ($limit != '')
            $query .= "LIMIT $limit";

        $run = $this->pdo->prepare($query);
        $run->execute($where_array);

        while ($row = $run->fetch(PDO::FETCH_ASSOC)) {
            $records[] = $row;
        }

        return $records;
    }

    // For android notification
    function android_notification($gcm_id, $msg) {
        if (!empty($this->android_GCM)) {
            $registrationIds = array($gcm_id);
            $message = array("msg" => $msg);

            $GOOGLE_API_KEY = $this->android_GCM;

            $fields = array
                (
                'registration_ids' => $registrationIds,
                'data' => $message
            );

            $headers = array
                (
                'Authorization: key=' . $GOOGLE_API_KEY,
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);

//            echo $result;
        }
    }

    function send_notification_iphone($registatoin_ids, $msg, $message) {
        
        $deviceToken = $registatoin_ids;

        // Put your private key's passphrase here:
        $passphrase = '12345';

        // Put your alert message here:
        $message = $message;

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/bookmwah/webservices/Test_Dev.pem');
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

        // Open a connection to the APNS server
        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);

        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);
        
        // Create the payload body
        $body['aps'] = array(
            'alert' => $message,
            'msg' => $msg, 'sound' => 'strings.wav'
        );

        // Encode the payload as JSON
        $payload = json_encode($body);

        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));

        if (!$result) {
            echo 'Message not delivered' . PHP_EOL;
        } else {
            echo 'Message successfully delivered' . PHP_EOL;
        }

        // Close the connection to the server
        fclose($fp);
    }

    // For IOS notification
    function push_iOS($token, $msg, $alert) {
        if (!empty($this->pem_Pro) && !empty($this->passPhrase)) {
            // Provide the Host Information.

            if (!empty($this->sandBox))
                $tHost = 'gateway.push.apple.com';
            else
                $tHost = 'gateway.sandbox.push.apple.com';
            
            $tPort = 2195;

            // Provide the Certificate and Key Data.

            if (!empty($this->sandBox))
                $tCert = $this->pem_Pro;
            else
                $tCert = $this->pem_Dev;

            // Provide the Private Key Passphrase

            $tPassphrase = $this->passPhrase;

            // Provide the Device Identifier (Ensure that the Identifier does not have spaces in it).

            $tToken = $token;

            // The message that is to appear on the dialog.

            $tAlert = $alert;

            // The Badge Number for the Application Icon (integer >=0).
            //            $tBadge = 8;
            // Audible Notification Option.

            $tSound = 'default';

            // The content that is returned by the LiveCode "pushNotificationReceived" message.

            $tPayload = 'Notification sent';

            // Create the message content that is to be sent to the device.

            $tBody['aps'] = array(
                'alert' => $tAlert,
                'msg' => $msg,
                //                'badge' => $tBadge,
                'sound' => $tSound,
            );

            $tBody ['payload'] = $tPayload;

            // Encode the body to JSON.

            $tBody = json_encode($tBody);

            // Create the Socket Stream.

            $tContext = stream_context_create();

            stream_context_set_option($tContext, 'ssl', 'local_cert', $tCert);

            // Remove this line if you would like to enter the Private Key Passphrase manually.

            stream_context_set_option($tContext, 'ssl', 'passphrase', $tPassphrase);

            // Open the Connection to the APNS Server.

            $tSocket = stream_socket_client('ssl://' . $tHost . ':' . $tPort, $error, $errstr, 30, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $tContext);

            // Check if we were able to open a socket.

            if (!$tSocket)
                exit("APNS Connection Failed: $error $errstr" . PHP_EOL);

            // Build the Binary Notification.

            $tMsg = chr(0) . chr(0) . chr(32) . pack('H*', $tToken) . pack('n', strlen($tBody)) . $tBody;

            // Send the Notification to the Server.

            $tResult = fwrite($tSocket, $tMsg, strlen($tMsg));

//                if ($tResult)
//            
//                echo 'Delivered Message to APNS' . PHP_EOL;
//            
//                else
//            
//                echo 'Could not Deliver Message to APNS' . PHP_EOL;
//             Close the Connection to the Server.

            fclose($tSocket);
        }
    }

    function json_output($array) {
        if (is_array($array)) {
            header('Content-Type: application/json');
            echo json_encode($array);
        }
    }

}

?>