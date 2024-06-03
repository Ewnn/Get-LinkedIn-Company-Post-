<?php

function refreshToken($refreshToken, $clientId, $clientSecret) {
    // API endpoint for token refresh
    $tokenUrl = 'https://www.linkedin.com/oauth/v2/accessToken';

    // POST request parameters
    $params = [
        'grant_type' => 'refresh_token',
        'refresh_token' => $refreshToken,
        'client_id' => $clientId,
        'client_secret' => $clientSecret
    ];

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt_array($ch, [
        CURLOPT_URL => $tokenUrl,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($params),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    // Execute the POST request
    $response = curl_exec($ch);

    // Close cURL session
    curl_close($ch);

    // Decode the JSON response
    $responseData = json_decode($response, true);

    // Return the new access token
    return $responseData['access_token'];
}


$refreshToken = '';
$clientId = '';
$clientSecret = '';

$newAccessToken = refreshToken($refreshToken, $clientId, $clientSecret);

file_put_contents("token.json", $newAccessToken);

?>
