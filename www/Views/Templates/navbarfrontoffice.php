<!DOCTYPE html>
<html>
<head>
    <title>Projet MVC</title>
</head>
<body>
    <h1>Projet PHP MVC</h1>
    <nav>
        <ul>
            <li><a href="/home">Accueil</a></li>
        <?php if ($_SESSION["role"]=== 'admin'): ?>
            <li><a href="/admin">Panel Admin</a></li>
        <?php endif; ?>
            <li><a href="/logout">Déconnexion</a></li>
        </ul>
    </nav>
    <?php include $this->viewPath;?>
</body>
</html>
