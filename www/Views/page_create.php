<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Créer une nouvelle page</title>
</head>
<body>
    <h2>Créer une nouvelle page</h2>

    <?php
        if(!empty($errors)){
            echo "<pre>";
            print_r($errors);
            echo "</pre>";
        }
    ?>

    <form method="post" action="/page_create">
        <input type="text" name="title" value="<?= $_POST["title"]??"" ?>" required placeholder="Titre"><br>
        <textarea name="content" required placeholder="Contenu"><?= $_POST["content"]??"" ?></textarea><br>
        <input type="submit" value="Créer la page">
    </form>
</body>
</html>