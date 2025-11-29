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

    <form method="post" action="/register">
        <input type="text" value="<?= $_POST["username"]??"" ?>"  name="username" placeholder="Username"><br>
        <input type="email" value="<?= $_POST["email"]??"" ?>"  required name="email" placeholder="Email"><br>
        <input type="password" required name="pwd" placeholder="Password"><br>
        <input type="password" required name="pwdConfirm" placeholder="Confirm Password"><br>
        <input type="submit" value="Register">
        <a href="/login">Déjà un compte ? Connectez-vous</a>
    </form>

    </body>
</html>