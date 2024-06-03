<?php
// Chemin vers votre fichier JSON
$jsonFile = '/var/www/joomla/templates/linkedin/merged.json';

// Vérifier si le fichier existe
if (!file_exists($jsonFile)) {
    die("Le fichier JSON n'existe pas.");
}

// Lire le contenu du fichier JSON
$jsonData = file_get_contents($jsonFile);

// Décoder les données JSON
$data = json_decode($jsonData, true);

// Vérifier si le JSON est valide
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Erreur dans le fichier JSON: " . json_last_error_msg());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LinkedIn-like Feed</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f3f2ef;
            margin: 0;
            padding: 0;
        }

        .feed-container {
            width: 60%;
            margin: auto;
            padding: 10px;
            overflow-y: scroll;
            max-height: 100vh;
            border: 1px solid #dcdcdc;
            background-color: #fff;
        }

        .feed-item {
            border-bottom: 1px solid #e6e9ec;
            padding: 15px 0;
        }

        .feed-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .author-time {
            display: flex;
            flex-direction: column;
        }

        .author {
            font-size: 14px;
            font-weight: 600;
            line-height: 20px;
            letter-spacing: .15px;
            color: #050505;
        }

        .time {
            color: #777;
            font-size: 0.8em;
        }

        .feed-content {
            margin-bottom: 10px;
        }

        #text{
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
            color: #050505;
        }

        .feed-image img,
        .feed-multi-images img {
            max-width: 100%;
            margin-bottom: 10px;
        }

        .feed-multi-images {
            display: flex;
            flex-wrap: wrap;
        }

        .feed-multi-images img {
            width: 48%;
            margin-right: 2%;
            margin-bottom: 10px;
        }

        .feed-multi-images img:nth-child(2n) {
            margin-right: 0;
        }

        .logo {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="feed-container">
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $post): ?>
            <div class="feed-item">
                <div class="feed-header">
                    <img src="" alt="Logo" class="logo">
                    <div class="author-time">
                        <span class="author">Your company</span>
                        <span class="time"><?= date('Y-m-d H:i:s', $post['createdAt'] / 1000) ?></span>
                    </div>
                </div>
                <div class="feed-content">
                    <?php if (isset($post['images'])): ?>
                        <div class="feed-image">
                            <?php foreach ($post['images'] as $image): ?>
                                <img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="Post Image">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($post['content']['multiImage'])): ?>
                        <div class="feed-multi-images">
                            <?php foreach ($post['content']['multiImage']['images'] as $image): ?>
                                <img src="<?= htmlspecialchars($image['id'], ENT_QUOTES, 'UTF-8') ?>" alt="Post Image">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <p class="post"><?= nl2br(htmlspecialchars($post['commentary'], ENT_QUOTES, 'UTF-8')) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune donnée disponible.</p>
        <?php endif; ?>
    </div>
</body>
</html>
