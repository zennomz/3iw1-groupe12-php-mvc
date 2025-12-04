<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= $page['title'] ?></title>
</head>
<body>
    <h1><?= $page['title'] ?></h1>
    <p>Publié par <?= $page['author_name'] ?> le <?= $page['date_created'] ?></p>
    <div>
        <?= nl2br($page['content']) ?>
    </div>
    <br>
    <br>
    <a href="/home">Retourner dans la liste des pages</a>
</body>
</html>