<?php

$token_file = '/var/www/joomla/templates/linkedin/token.json';
$data_file = '/var/www/joomla/templates/linkedin/data.json';

// Lecture du contenu du fichier token.json
$api_key = file_get_contents($token_file);

// Vérification si le contenu du fichier est vide ou non
if (empty($api_key)) {
    echo "Erreur: Impossible de lire le token depuis le fichier.";
    exit;
}

// API endpoint URL
$url = 'https://api.linkedin.com/rest/posts?author=urn%3Ali%3Aorganization%3A18860499&q=author&count=5&sortBy=LAST_MODIFIED';

// Initialize cURL session
$curl = curl_init();

// Set cURL options
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return response as a string
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'LinkedIn-Version:' . '202401',
    'Authorization: Bearer ' . $api_key,
    'X-Restli-Protocol-Version:' . '2.0.0',
));

// Execute cURL request and get the response
$response = curl_exec($curl);

// Check for cURL errors
if ($response === false) {
    echo 'cURL Error: ' . curl_error($curl);
} else {
    // Close cURL session
    curl_close($curl);

    // Write the response to the data file
    $result = file_put_contents($data_file, $response);

    if ($result !== false) {
        echo "Les données ont été écrites dans le fichier data.json avec succès.";
    } else {
        echo "Erreur lors de l'écriture des données dans le fichier data.json.";
    }
}
?>
