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
                    <p>Crée le <?= $page['date_created'] ?></p>
                    <p>Par l'auteur : <?= $page['author_name'] ?></p>
                    <a href="/page/<?= urlencode($page['slug']) ?>">Voir</a>
                    <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == $page['author_id']): ?>
                        | <a href="/edit_page/<?= urlencode($page['slug']) ?>">Éditer</a>
                        | <a href="/delete_page/<?= urlencode($page['slug']) ?>" onclick="return confirm('Supprimer cette page ?');">Supprimer</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</body>
</html>