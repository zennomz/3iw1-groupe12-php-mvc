<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
</head>
<body>
    <h1>Liste des pages</h1>
    <br>
    <a href="/page_create">Créer une nouvelle page</a>
    <br>
    
    <?php if (empty($pages)): ?>
        <p>Aucune page disponible.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($pages as $page): ?>
                <li>
                    <h2><?= $page['title'] ?></h2>
                    <p>Créer le <?= $page['date_created'] ?></p>
                    <p>Par l'auteur : <?= $page['author_name'] ?></p>
                    <a href="/page/<?= $page['slug'] ?>">Voir</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</body>
</html>