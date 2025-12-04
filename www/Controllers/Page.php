<?php 

namespace App\Controllers;

use App\Core\Render;
use App\Models\PageModel;

class Page
{

    public function list(): void
    {
        session_start();
        if (empty($_SESSION["user_id"])) {
            header("Location: /login");
            exit();
        }

        require "../db.php";

        $pageModel = new PageModel($pdo);
        $pages = $pageModel->getAllPages();

        $render = new Render("home", "frontoffice");
        $render->assign("pages", $pages);
        $render->render();
    }

    public function view(): void
    {
        session_start();
        if (empty($_SESSION["user_id"])) {
            header("Location: /login");
            exit();
        }
        
        require "../db.php";
        
        if (!isset($_GET['id'])) {
            die ('Page introuvable');
        }

        $id = (int)$_GET['id'];
        $pageModel = new PageModel($pdo);
        $page = $pageModel->getPageById($id);

        if (!$page) {
            die ('Page introuvable');
        }

        $render = new Render("page_view", "frontoffice");
        $render->assign("page", $page);
        $render->render();
    }

    public function create(): void
    {
        session_start();
        if (empty($_SESSION["user_id"])) {
            header("Location: /login");
            exit();
        }

        require "../db.php";
        $pageModel = new PageModel($pdo);

        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST"
            && !empty($_POST["title"])
            && !empty($_POST["content"])
        ){
            $title = trim($_POST["title"]);
            $content = trim($_POST["content"]);
            $authorId = $_SESSION["user_id"] ?? null;

            if (empty($title) || empty($content)) {
                $errors[] = "Tous les champs sont obligatoires.";
            }

            if (strlen($title) > 100) {
                $errors[] = "Le titre ne peut pas dépasser 100 caractères.";
            }

            if (empty($errors)) {
                $pageModel->createPage($title, $content, $authorId);
                header("Location: /pages");
                exit();
            }
        }

        $render = new Render("page_create", "backoffice");
        $render->assign("errors", $errors);
        $render->render();
    }
}