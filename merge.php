<?php
// Lire le contenu des fichiers JSON
$dataJson = file_get_contents('data.json');
$imagesJson = file_get_contents('img_urls.json');

// Décoder les fichiers JSON en tableaux PHP
$data = json_decode($dataJson, true);
$images = json_decode($imagesJson, true);

// Vérifier les erreurs de décodage JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    die('Erreur de décodage JSON : ' . json_last_error_msg());
}

// Créer un tableau associatif pour les images
$imageMap = [];
foreach ($images as $image) {
    $imageMap[$image['id']] = $image['downloadUrl'];
}

// Fusionner les données
$combinedData = [];
foreach ($data['elements'] as $publication) {
    $media = $publication['content'];
    $publicationImages = [];

    // Vérifier si l'ID de l'image dans la publication existe dans le tableau des images
    if (isset($media['media']) && isset($imageMap[$media['media']['id']])) {
        $publicationImages[] = $imageMap[$media['media']['id']];
    } elseif (isset($media['multiImage']) && isset($media['multiImage']['images'])) {
        foreach ($media['multiImage']['images'] as $image) {
            if (isset($imageMap[$image['id']])) {
                $publicationImages[] = $imageMap[$image['id']];
            }
        }
    }

    $publication['images'] = $publicationImages;
    $combinedData[] = $publication;
}

// Convertir le contenu fusionné en JSON
$mergedJson = json_encode($combinedData, JSON_PRETTY_PRINT);

// Enregistrer le contenu JSON dans un fichier
$file_path = 'merged.json';
if (file_put_contents($file_path, $mergedJson) !== false) {
    echo "Le fichier 'merged.json' a été créé avec succès.";
} else {
    echo "Erreur lors de la création du fichier 'merged.json'.";
}
?>
