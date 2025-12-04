<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
</head>
<body>
    <a href = "/admin">Panel admin</a>
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
                    <a href="/page_view?id=<?= $page['id'] ?>">
                        <?= $page['title'] ?>
                        <?= $page['date_created'] ?>
                        <?= $page['author_name'] ?>
                    </a>
                    <a href="/page_view?id=<?= $page['id'] ?>">Voir</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</body>
</html>