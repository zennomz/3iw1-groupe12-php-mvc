<?php
namespace App\Controllers;

use App\Core\Render;

class Auth
{
    public function login(): void
    {

        $render = new Render("login", "backoffice");
        $render->render();
    }

    public function register(): void
    {
        $render = new Render("register", "backoffice");
        $render->render();
    }

    public function password_reset(): void
    {
        $render = new Render("password_reset", "backoffice");
        $render->render();
    }

}