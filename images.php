<?php

// Function to fetch image ID from API using cURL with headers and API key
function fetchImageIdFromApi($url, $apiKey) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, true); // Treat HTTP errors as failures

    // Set headers including the API key
    $headers = [
        'Content-Type: application/json',
        'LinkedIn-Version: 202401',
        'Authorization: Bearer ' . $apiKey,
        'X-Restli-Protocol-Version: 2.0.0'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        // Handle cURL error
        error_log('cURL error: ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    curl_close($ch);

    $data = json_decode($response, true);

    // Attempt to find the image ID in the same structure as data.json
    if (isset($data['content']['multiImage']['images'][0]['id'])) {
        return $data['content']['multiImage']['images'][0]['id'];
    } elseif (isset($data['content']['article']['thumbnail'])) {
        return $data['content']['article']['thumbnail'];
    } elseif (isset($data['content']['media']['id'])) {
        return $data['content']['media']['id'];
    }

    return null;
}

$token = '/var/www/joomla/templates/linkedin/token.json';

// Read the JSON data from the file
$json = file_get_contents('/var/www/joomla/templates/linkedin/data.json');
$data = json_decode($json, true);

// Initialize an array to collect image IDs
$imageIds = [];

// Define your API key
$apiKey = file_get_contents($token);

// Check if the required keys exist in the data
if (isset($data['elements'])) {
    // Access the elements array
    $elements = $data['elements'];

    // Loop through each element
    foreach ($elements as $element) {
        $imageId = null;

        // Check if the element has multiImage content and get all image IDs
        if (isset($element['content']['multiImage']['images'])) {
            foreach ($element['content']['multiImage']['images'] as $image) {
                $imageIds[] = $image['id'];
            }
        }
        // Check if the element has article content and get the thumbnail ID
        elseif (isset($element['content']['article']['thumbnail'])) {
            $imageId = $element['content']['article']['thumbnail'];
        }
        // Check if the element has media content and get the media ID
        elseif (isset($element['content']['media']['id'])) {
            $imageId = $element['content']['media']['id'];
        }
        // Check if the element has a reshareContext root
        elseif (isset($element['reshareContext']['root'])) {
            $root = $element['reshareContext']['root'];
            // Check if the root starts with 'urn:li:share'
            if (strpos($root, 'urn:li:share') === 0) {
                // Construct the API endpoint using the root value
                $apiUrl = "https://api.linkedin.com/rest/posts/" . urlencode($root);

                // Fetch the data from the API endpoint
                $imageId = fetchImageIdFromApi($apiUrl, $apiKey);
            } else {
                $imageId = $root;
            }
        }

        // Add the found image ID or the element's root ID to the array
        if ($imageId !== null) {
            $imageIds[] = $imageId;
        }
    }

    // Convert the image IDs array to JSON
    $outputJson = json_encode($imageIds, JSON_PRETTY_PRINT);

    // Write the JSON to a file
    file_put_contents('/var/www/joomla/templates/linkedin/images.json', $outputJson);
    echo('Les IDs des images ont été écrites dans le fichier image.json avec succès');
} else {
    echo "No elements found";
}
