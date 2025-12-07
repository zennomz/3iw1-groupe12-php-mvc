<!DOCTYPE html>
<html>
<head>
    <title>Projet MVC</title>
</head>
<body>
    <h1>Panel Admin - Projet PHP MVC</h1>
    <nav>
        <ul>
            <li><a href="/home">Accueil</a></li>
            <li><a href="/admin">Panel Admin</a></li>
            <li><a href="/logout">Déconnexion</a></li>
        </ul>
    </nav>
    <?php include $this->viewPath;?>
</body>
</html>
