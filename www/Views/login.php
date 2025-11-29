<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Projet PHP</title>
    </head>
    <body>

    <?php
        if(!empty($errors)){
            echo "<pre>";
            print_r($errors);
            echo "</pre>";
        }
    ?>

    <form method="post" action="/login">
        <input type="email" value="<?= $_POST["email"]??"" ?>"  required name="email" placeholder="Email"><br>
        <input type="password" required name="pwd" placeholder="Password"><br>
        <input type="submit" value="Login">
        <a href="/register">Pas encore de compte ? Inscrivez-vous</a>
        <a href="/password_reset">Mot de passe oublié ?</a>
    </form>

    </body>
</html>