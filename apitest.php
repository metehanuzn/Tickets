<?php
$ch = curl_init();

$data = [
    "EMAIL" => "TEST@mail.com",
    "PASSWORD" => "TEST"
];

curl_setopt($ch, CURLOPT_URL, "http://localhost/api/login_api.php");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

echo $response;