<?php
// connection to database
require('./connect.php');

// All the data that was posted during the API call:
$data = json_decode(file_get_contents("php://input"), true);

// name of the request posted during API call:
$request = $con->real_escape_string($data['request']);

if ($request == "Daraja Payment") {



    $phoneNumber = $con->real_escape_string($data['phone']);
    $amount  = $con->real_escape_string($data['amount']);
    $paybill  = $con->real_escape_string($data['payBill']);
    $email = $con->real_escape_string($data['email']);

    // Default Message to display on stk menu if none is provided
    $promptMessage = 'Payment for some service offered';

    // Custom message to display on STK menu
    $prompt = $con->real_escape_string($data['promptMessage']);

    if ($prompt) {
        $promptMessage = $prompt;
    }

    // convert the posted phone number to 254 format
    $shortPhone = substr($phoneNumber, 1);
    $phone = "254" . $shortPhone;


    //daraja
    $consumerKey = '9xVvsUCX3scvjJ0pmU8esbeAgoi7ELvd'; //Fill with your app Consumer Key
    $consumerSecret = '4sE2TpgJmYfAc0eA'; // Fill with your app Secret

    $headers = ['Content-Type:application/json; charset=utf8'];

    // safaricom might change this url. If so, get the valid one from daraja api docs
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





    // safaricom might change this url. If so, get the valid one from daraja api docs
    $initiate_url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

    $BusinessShortCode = "$paybill";
    $Timestamp = date('YmdHis');
    $PartyA = "$phone";
    $AccountReference = $promptMessage;
    $Amount = "$amount";
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
        'Amount' => $amount,
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
    echo $curl_response;
}