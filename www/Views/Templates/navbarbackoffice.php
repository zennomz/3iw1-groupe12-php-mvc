<!DOCTYPE html>
<html>
<head>
    <title>Projet MVC</title>
</head>
<body>
    <h1>Panel Admin - Projet PHP MVC</h1>
    <nav>
        <ul>
            <li><a href="/home">Quitter le Panel</a></li>
            <li><a href="/manage_users">Gestion des utilisateurs</a></li>
            <li><a href="/manage_pages">Gestion des pages</a></li>
            <li><a href="/logout">Déconnexion</a></li>
        </ul>
    </nav>
    <?php include $this->viewPath;?>
</body>
</html>
