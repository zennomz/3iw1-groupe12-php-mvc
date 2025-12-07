<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestion des pages</title>
</head>
<body>
    <h2>Gérer les pages</h2>
    <a href="/admin">Retour dans le panel admin</a>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Auteur</th>
                <th>Date de création</th>
                <th>Date de mise à jour</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $page): ?>
                <tr>
                    <form method="post">
                        <td><?= $page['id'] ?></td>
                        <td>
                            <input type="text" name="title" value="<?= $page['title'] ?>" required>
                        </td>
                        <td>
                            <textarea name="content" required><?= $page['content'] ?></textarea>
                        </td>
                        <td><?= $page['author_name'] ?></td>
                        <td><?= $page['date_created'] ?></td>
                        <td><?= $page['date_updated'] ?></td>
                        <td>
                            <input type="hidden" name="page_id" value="<?= $page['id'] ?>">
                            <input type="hidden" name="action" value="update">
                            <input type="submit" value="Mettre à jour">
                    </form>
                    <form method="post">
                        <input type="hidden" name="page_id" value="<?= $page['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit">Supprimer</button>
                    </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>