<?php

namespace App\Controllers;

use App\Core\Render;
use App\Core\Database;
use App\Models\PageModel;

class Base
{

    public function index(): void
    {
        $render = new Render("home_login", "authentification");
        $render->render();
    }

    public function home(): void
    {
        session_start();
        if (empty($_SESSION["user_id"])) {
            header("Location: /login");
            exit();
        }
        
        $pdo = Database::getConnection();

        $pageModel = new PageModel($pdo);
        $pages = $pageModel->getAllPages();

        $render = new Render("home", "navbarfrontoffice");
        $render->assign("pages", $pages);
        $render->render();
    }

}