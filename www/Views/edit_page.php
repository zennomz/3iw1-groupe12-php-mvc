<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Éditer la page</title>
</head>
<body>
    <h1>Éditer la page</h1>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li style="color: red;"><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a href="/home">Retourner dans la liste des pages</a>
    <br>
    <br>
    
    <form method="post" action="/edit_page/<?= urlencode($page['slug']) ?>">
        <label for="title">Titre :</label><br>
        <input type="text" id="title" name="title" value="<?= $page['title'] ?>" required><br><br>

        <label for="content">Contenu :</label><br>
        <textarea id="content" name="content" rows="10" cols="50" required><?= $page['content'] ?></textarea><br><br>

        <input type="hidden" name="action" value="update">
        <input type="hidden" name="page_id" value="<?= (int)$page['id'] ?>">

        <input type="submit" value="Mettre à jour la page">
    </form>
</body>
</html>