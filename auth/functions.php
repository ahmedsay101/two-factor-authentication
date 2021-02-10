<?php

function lastValue($input) {
    if(isset($_POST[$input])) {
        echo $_POST[$input];
    }
}
function clear($text) {
    $text = strip_tags($text);
    $text = trim($text);
    $text = htmlspecialchars($text);
    return $text;
}
function clearText($text) {
    $text = clear($text);
    $text = str_replace(" ", "", $text);
    $text = strtolower($text);
    $text = ucfirst($text);
    return $text;
}
function clearEmail($email) {
    $email = str_replace(" ", "", $email);
    $email = clear($email);
    return $email;
}
function validateFirstName($firstName) {
    $errors = array();
    if(strlen($firstName) > 25 || strlen($firstName) < 2) {
        array_push($errors, "<span class='error' data-errorType='firstName'>Your first name must be between 2 and 25 character</span>");
    }
    return $errors;
}
function validateLastName($lastName) {
    $errors = array();
    if(strlen($lastName) > 25 || strlen($lastName) < 2) {
        array_push($errors, "<span class='error' data-errorType='lastName'>Your last name must be between 2 and 25 character</span>");
    }
    return $errors;
}
function validateEmail($con, $email, $confirmation) {
    $errors = array();
    if($email != $confirmation) {
        array_push($errors, "<span class='error' data-errorType='email'>Your emails do not match</span>");
        return $errors;
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "<span class='error' data-errorType='email'>Please enter a valid email adress</span>");
        return $errors;
    }

    $query = $con->prepare("SELECT email FROM users WHERE email=:em");
    $query->bindParam(":em", $email);
    $query->execute();

    if($query->rowCount() != 0) {
        array_push($errors, "<span class='error' data-errorType='email'>This Email is already exists</span>");
        return $errors;
    } 
    return $errors;
}
function validatePasswords($password, $confirmation) {
    $errors = array();
    if($password != $confirmation) {
        array_push($errors, "<span class='error'>Your Passwords do not match</span>");
        return $errors;
    }
    if(preg_match("/[^A-za-z0-9]/", $password)) {
        array_push($errors, "<span class='error'>Your password can only contain numbers and letters</span>");
        return $errors;
    }
    if(strlen($password) > 30 || strlen($password) < 5) {
        array_push($errors, "<span class='error'>Your password must be between 5 and 30 characters</span>");
        return $errors;
    }
    return $errors;
}
function sendMessage($msg, $phone) {
    $data = 'username=sandbox&to='.$phone.'&message='.$msg.'&from=34020';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://api.sandbox.africastalking.com/version1/messaging");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/x-www-form-urlencoded', 'apiKey: 85c6794bf305950074a31a02d86b98401eaf3057a02dbec6330166535e923961'));
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $apiResponse = curl_exec($curl);

    $response = array();
    $response["curlStatusCode"] = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    $phpApiResponse = json_decode($apiResponse);
    
    $response["apiStatus"]= $phpApiResponse->SMSMessageData->Recipients[0]->status;
    $response["apiStatusCode"] = $phpApiResponse->SMSMessageData->Recipients[0]->statusCode;

    curl_close ($curl);

    return $response;
}

/*function sendMessage() {
    $tuCurl = curl_init();
    curl_setopt($tuCurl, CURLOPT_URL, "https://api.sandbox.africastalking.com/version1/messaging");
    curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array('Accept: application/json', 'Content-Type: application/x-www-form-urlencoded', 'apiKey: 85c6794bf305950074a31a02d86b98401eaf3057a02dbec6330166535e923961'));
    curl_setopt($tuCurl, CURLOPT_POST, 1);
    curl_setopt($tuCurl, CURLOPT_POSTFIELDS, 'username=sandbox&to=+201015023876&message=Hello%20World!&from=34020');

    $apiResponse = curl_exec($tuCurl);

    $response = array();
    $response["curlStatusCode"] = curl_getinfo($tuCurl, CURLINFO_HTTP_CODE);

    $phpApiResponse = json_decode($apiResponse);
    
    $response["apiStatus"]= $phpApiResponse->SMSMessageData->Recipients[0]->status;
    $response["apiStatusCode"] = $phpApiResponse->SMSMessageData->Recipients[0]->statusCode;

    curl_close ($tuCurl);

    return $response;
}*/


?>
