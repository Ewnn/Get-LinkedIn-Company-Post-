<?php

$token = '/var/www/joomla/templates/linkedin/token.json';

// Path to your JSON file
$jsonFilePath = '/var/www/joomla/templates/linkedin/images.json';

// Your API key
$api_key = file_get_contents($token);

// Define the output JSON file
$outputFile = '/var/www/joomla/templates/linkedin/img_urls.json';

// Read the JSON file
$jsonContent = file_get_contents($jsonFilePath);

// Decode the JSON content to an array
$endpoints = json_decode($jsonContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error decoding JSON: ' . json_last_error_msg());
}

// Check if the decoded JSON is an array
if (!is_array($endpoints)) {
    die('Invalid JSON format.');
}

// Initialize the extracted data array
$extractedData = [];

// Iterate through all endpoints to process each one
foreach ($endpoints as $endpoint) {
    // URL encode the endpoint
    $encodedEndpoint = urlencode($endpoint);

    // Initialize default blank object
    $data = [
        'id' => $endpoint,
        'downloadUrl' => '',
        'owner' => '',
        'downloadUrlExpiresAt' => '',
        'status' => ''
    ];

    // If the endpoint contains 'urn:li:image', process it
    if (strpos($endpoint, 'urn:li:image') !== false) {
        // Your base URL for the API
        $baseUrl = 'https://api.linkedin.com/rest/images?ids=List(';

        // Construct the full URL
        $fullUrl = $baseUrl . $encodedEndpoint . ')';

        // Initialize cURL
        $ch = curl_init();

        // Set the URL and other options
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'LinkedIn-Version: 202401',
            'X-Restli-Protocol-Version: 2.0.0',
            'Authorization: Bearer ' . $api_key,
            
        ));

        // Execute the request
        $response = curl_exec($ch);

        // Check for cURL errors
        if ($response === false) {
            die('cURL error: ' . curl_error($ch));
        }

        // Close cURL session
        curl_close($ch);

        // Decode the API response
        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            die('Error decoding API response JSON: ' . json_last_error_msg());
        }

        // Check if the endpoint data exists in the response
        if (isset($responseData['results'])) {
            foreach ($responseData['results'] as $result) {

                // Check if the endpoint exists in the response
                if (isset($result['id']) && $result['id'] === $endpoint) {
                    $apiData = $result;
                    if (isset($apiData['downloadUrl'], $apiData['owner'], $apiData['id'], $apiData['downloadUrlExpiresAt'], $apiData['status'])) {
                        // Update the data with the API response data
                        $data = [
                            'id' => $apiData['id'],
                            'downloadUrl' => $apiData['downloadUrl'],
                            'owner' => $apiData['owner'],
                            'downloadUrlExpiresAt' => $apiData['downloadUrlExpiresAt'],
                            'status' => $apiData['status']
                        ];
                    }
                }
            }
        }
    }

    // Add the data to the extracted data array
    $extractedData[] = $data;
}

// Write the extracted data to the output JSON file
file_put_contents($outputFile, json_encode($extractedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// Print the extracted data
echo json_encode($extractedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
