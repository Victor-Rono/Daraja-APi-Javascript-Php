<?php
// connection to database
require('./connect.php');

// All the data that was posted during the API call
$data = json_decode(file_get_contents("php://input"), true);

$request = $con->real_escape_string($data['request']);

if ($request == "activation") {
    $phone = $con->real_escape_string($data['phone']);
    $fee  = $con->real_escape_string($data['amount']);
    $paybill  = $con->real_escape_string($data['paybill']);
    $email = $con->real_escape_string($data['email']);

    $attempts = $con->query("SELECT * FROM activation_attempts WHERE `email` = '{$email}' && `phone` = '{$phone}'");
    $attemptCount = $attempts->num_rows;

    if ($attemptCount > 0) {
        //if they have attempted activation before
        $row = $attempts->fetch_assoc();
        $totalAttempts = $row['attempts'];
        $newAttempt = $totalAttempts + 1;
        $updateAttempts = $con->query("UPDATE activation_attempts SET `attempts` = '{$newAttempt}' WHERE `phone` = '{$phone}'");
    } else {
        $firstAttempt = $con->query("INSERT INTO activation_attempts(`email`,`phone`) VALUES('{$email}','{$phone}')");
    }

    //daraja
    $consumerKey = '9xVvsUCX3scvjJ0pmU8esbeAgoi7ELvd'; //Fill with your app Consumer Key
    $consumerSecret = '4sE2TpgJmYfAc0eA'; // Fill with your app Secret

    $headers = ['Content-Type:application/json; charset=utf8'];

    $access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

    $curl = curl_init($access_token_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
    $result = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $result = json_decode($result);

    $access_token = $result->access_token;





    $initiate_url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

    $BusinessShortCode = "$paybill";
    $Timestamp = date('YmdHis');
    $PartyA = "$phone";
    $AccountReference = "FNFCOM MEMBERSHIP FEE";
    $Amount = "$fee";
    $CallBackURL = 'https://fnfcom.com/daraja/validation_url.php';
    $passkey = 'b0d48bf7c866ab97f6c2d92433cd5d4dfa62b63c406b8b2b7c75df6cfd03fa33';
    $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);


    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $initiate_url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization:Bearer ' . $access_token)); //setting custom header


    $curl_post_data = array(
        //Fill in the request parameters with valid values
        'BusinessShortCode' => $BusinessShortCode,
        'Password' => $Password,
        'Timestamp' => $Timestamp,
        'TransactionType' => 'CustomerPayBillOnline',
        'Amount' => $fee,
        'PartyA' => $phone,
        'PartyB' => $BusinessShortCode,
        'PhoneNumber' => $PartyA,
        'CallBackURL' => $CallBackURL,
        'AccountReference' => $AccountReference,
        'TransactionDesc' => 'Registration Payment for FNFCOM'
    );

    $data_string = json_encode($curl_post_data);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

    $curl_response = curl_exec($curl);
    print_r($curl_response);

    echo $curl_response;
}
