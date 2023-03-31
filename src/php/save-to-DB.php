<?php
require('connect.php');

// name of table you want to save data to
$table = 'daraja-api-records';

//header("Content-Type: application/json");

// Save the M-PESA input stream. 
$mpesaResponse = file_get_contents('php://input');

$currentAmountQuery = $con->query("SELECT * FROM details LIMIT 1")->fetch_assoc();
$currentAmount = $currentAmountQuery['membership'];

// log the response
$logFile = "val.json";

// will be used when we want to save the response to database for our reference
$jsonMpesaResponse = json_decode($mpesaResponse, true);

$info = $jsonMpesaResponse;
$code = $info['Body']['stkCallback']["ResultCode"];
$description = $info['Body']['stkCallback']["ResultDesc"];
$amount = $info['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
$transaction = $info['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
$fullPhone = '0' . substr($info['Body']['stkCallback']['CallbackMetadata']['Item'][3]['Value'], 3);
$fullPhone1 = '0' . substr($info['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'], 3);

// insert to DB
$saveToDB = mysqli_query($con, "INSERT INTO $table (`Phone`,`Phone2`,`transaction_code`,`info`, `amount`) VALUES('{$fullPhone}','{$fullPhone1}','{$transaction}','{$description}','{$amount}')");

if ($saveToDB == 1) {
    $response = "Saved to Database Successfully";
} else {
    $response = "Failed to save to Database";
}
// return response
echo json_encode($response);