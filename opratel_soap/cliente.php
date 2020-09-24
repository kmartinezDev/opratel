<?php
include_once('lib/nusoap.php');

$client = new nusoap_client('http://localhost/servicio.php?wsdl');

$username = 'kmartine05z';
$password = 'secret';
$email= 'keller_martinez@hotmail.com';

$params = compact('username', 'password', 'email');

echo "ADD USER: \n";
$response = $client->call('addUser', $params);
print_r($response."\n");

echo "ACTIVATE USER: \n";
$response = $client->call('activateUser', ['username'=>$username]);
print_r($response."\n");

echo "DEACTIVATE USER: \n";
$response = $client->call('deactivateUser', ['username'=>$username]);
print_r($response."\n");

echo "GET USER: \n";
$response = $client->call('getUser', ['username'=>$username]);
print_r($response."\n");

// $xml = simplexml_load_string($response);
// print_r($xml);
?>