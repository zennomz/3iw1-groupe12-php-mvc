<?php

namespace App\Controllers;

use App\Core\Render;

class Admin
{

    public function index(): void
    {
        $render = new Render("admin", "backoffice");
        $render->render();
    }
    
    public function manage_users(): void
    {
        session_start();
        if (empty($_SESSION["user_id"])) {
            header("Location: /login");
            exit();
        }

        require "../db.php";

        if (isset($_POST["action"]) && $_POST["action"] === "update") {
            $id = (int)$_POST["user_id"];
            $username = trim($_POST["username"]);
            $isActive = isset($_POST["is_active"]) ? 1 : 0;

            $sql = 'UPDATE public."user" SET username = :username, is_active = :is_active WHERE id = :id';
            $queryPrepared = $pdo->prepare($sql);
            $queryPrepared->execute([
                "username" => $username,
                "is_active" => $isActive,
                "id" => $id
            ]);
            header("Location: /manage_users");
            exit();
        }

        if (isset($_POST["action"]) && $_POST["action"] === "delete") {
            $id = (int)$_POST["user_id"];

            $sql = 'DELETE FROM public."user" WHERE id = :id';
            $queryPrepared = $pdo->prepare($sql);
            $queryPrepared->execute([
                "id" => $id
            ]);
            header("Location: /manage_users");
            exit();
        }

        $sql = 'SELECT id, username, email, is_active, date_created FROM public."user" ORDER BY id ASC';
        $queryPrepared = $pdo->prepare($sql);
        $queryPrepared->execute();
        $users = $queryPrepared->fetchAll();

        $render = new Render("manage_users", "backoffice");
        $render->assign("users", $users);
        $render->render();
    }
}