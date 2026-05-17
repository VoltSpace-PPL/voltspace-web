<?php
$ch = curl_init('http://127.0.0.1:8000/api/auth/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email'=>'admin@voltspace.id', 'password'=>'password']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);
$response = curl_exec($ch);
echo "Login Response: " . $response . "\n";

$data = json_decode($response, true);
if (isset($data['token'])) {
    $token = $data['token'];
    
    $ch2 = curl_init('http://127.0.0.1:8000/api/dashboard');
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    $response2 = curl_exec($ch2);
    echo "Dashboard Response: " . $response2 . "\n";
} else {
    echo "No token received!\n";
}
