<?php

namespace App\Controllers;

use App\Core\Render;
use App\Models\UserModel;

class Admin
{

    public function index(): void
    {
        session_start();
        if (empty($_SESSION["user_id"])) {
            header("Location: /login");
            exit();
        }
        
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
        $userModel = new UserModel($pdo);

        if (isset($_POST["action"]) && $_POST["action"] === "update") {
            $id = (int)$_POST["user_id"];
            $username = trim($_POST["username"]);
            $isActive = isset($_POST["is_active"]) ? 1 : 0;

            $userModel->updateUser($id, $username, $isActive);
            header("Location: /manage_users");
            exit();
        }

        if (isset($_POST["action"]) && $_POST["action"] === "delete") {
            $id = (int)$_POST["user_id"];

            $userModel->deleteUser($id);
            header("Location: /manage_users");
            exit();
        }

        $users = $userModel->getAllUsers();

        $render = new Render("manage_users", "backoffice");
        $render->assign("users", $users);
        $render->render();
    }

    public function manage_pages(): void
    {
        session_start();
        if (empty($_SESSION["user_id"])) {
            header("Location: /login");
            exit();
        }

        require "../db.php";
        $pageModel = new \App\Models\PageModel($pdo);

        if (isset($_POST["action"]) && $_POST["action"] === "update") {
            $id = (int)$_POST["page_id"];
            $title = trim($_POST["title"]);
            $content = trim($_POST["content"]);

            $pageModel->updatePage($id, $title, $content);
            header("Location: /manage_pages");
            exit();
        }
        
        if (isset($_POST["action"]) && $_POST["action"] === "delete") {
            $id = (int)$_POST["page_id"];

            $pageModel->deletePage($id);
            header("Location: /manage_pages");
            exit();
        }

        $pages = $pageModel->getAllPages();
        
        $render = new Render("manage_pages", "backoffice");
        $render->assign("pages", $pages);
        $render->render();
    }
}