<?php
$response = new stdClass();
$response->atatus = 200;
$response->message = 'OK';
$response->data =$_POST;
header('Content-Type: application/json');
header('HTTP/1.0 ' . $response->atatus . ' ' . $response->message);
die(json_encode($response));

