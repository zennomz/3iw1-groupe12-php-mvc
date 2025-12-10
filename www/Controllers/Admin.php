<?php

namespace App\Controllers;

use App\Core\Render;
use App\Core\Database;
use App\Models\UserModel;
use App\Models\PageModel;

class Admin
{

    public function index(): void
    {
        session_start();
        if (empty($_SESSION["user_id"])) {
            header("Location: /login");
            exit();
        }

        if ($_SESSION["role"] !== "admin") {
            header("Location: /home");
            exit();
        }


        $render = new Render("admin", "navbarbackoffice");
        $render->render();
    }

    public function manage_users(): void
    {
        session_start();
        if (empty($_SESSION["user_id"])) {
            header("Location: /login");
            exit();
        }

        if ($_SESSION["role"] !== "admin") {
            header("Location: /home");
            exit();
        }

        $pdo = Database::getConnection();
        $userModel = new UserModel($pdo);

        if (isset($_POST["action"]) && $_POST["action"] === "update") {
            $id = (int)$_POST["user_id"];
            $username = trim($_POST["username"]);
            $isActive = isset($_POST["is_active"]);
            $role = trim($_POST["role"]);

            $userModel->updateUser($id, $username, $role, $isActive);

            session_destroy();
            session_start();
            $_SESSION["user_id"] = $id;
            $_SESSION["role"] = $role;

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

        $render = new Render("manage_users", "navbarbackoffice");
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

        if ($_SESSION["role"] !== "admin") {
            header("Location: /home");
            exit();
        }
        $pdo = Database::getConnection();
        $pageModel = new PageModel($pdo);

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

        $render = new Render("manage_pages", "navbarbackoffice");
        $render->assign("pages", $pages);
        $render->render();
    }
}
