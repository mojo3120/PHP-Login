<?php
// sendmessage.php
use Bluerhinos\phpMQTT;
require("../../login/vendor/bluerhinos/phpmqtt/phpMQTT.php");
$server = "m13.cloudmqtt.com";     // change if necessary
$port = 14266;                     // change if necessary
$username = "nqtznmdh";                   // set your username
$password = "9s-KmvMFJzgX";                   // set your password
$client_id = "phpMQTT-publisher"; // make sure this is unique for connecting to sever - you could use uniqid()

$errors         = array();      // array to hold validation errors
$data           = array();      // array to pass back data

// validate the variables ======================================================
    // if any of these variables don't exist, add an error to our $errors array

    if (empty($_POST['message']))
        $errors['message'] = 'Message is required.';

    if (empty($_POST['minutes']))
        $errors['minutes'] = 'Time is required.';

    if (empty($_POST['serial']))
        $errors['serial'] = 'No signboards selected.';

// return a response ===========================================================

    // if there are any errors in our errors array, return a success boolean of false
    if ( ! empty($errors)) {

        // if there are items in our errors array, return those errors
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {

        // if there are no errors process our form, then return a message
        $msg = $_POST['message'];
        $time = $_POST['minutes'];
        $signString = $_POST['serial'];
            $signArray = explode(',', $signString);
        
        
        $mqtt = new phpMQTT($server, $port, $client_id);
        if ($mqtt->connect(true, NULL, $username, $password)) {
            foreach ($signArray as $sign) {
                $mqtt->publish("signboard/".$sign."/settings/scrollminutes", $time, 0);
                $mqtt->publish("signboard/".$sign."/alert", $msg, 0);
            }
        $mqtt->close();

        } else {
            //echo "Time out!\n";
            //todo - process mqtt error
        }
        

        // show a message of success and provide a true success variable
        $data['success'] = true;
        $data['message'] = 'Success!';
    }

    // return all our data to an AJAX call
    echo json_encode($data);