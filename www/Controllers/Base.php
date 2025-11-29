<?php

namespace App\Controllers;

use App\Core\Render;

class Base
{

    public function index(): void
    {
        $render = new Render("home_login", "frontoffice");
        $render->render();
    }

    public function home(): void
    {
        $render = new Render("home", "frontoffice");
        $render->render();
    }

}