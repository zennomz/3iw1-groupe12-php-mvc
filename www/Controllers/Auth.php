<?php
namespace App\Controllers;

use App\Core\Render;

class Auth
{
    public function login(): void
    {
        require "../db.php";

        $errors = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST"
            && count($_POST)==2
            && !empty($_POST["email"])
            && !empty($_POST["pwd"])
        ){
            $email = strtolower(trim($_POST["email"]));
            $pwd = $_POST["pwd"];

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors[]="Le format de l'email est invalide";
            }else{
                $sql = 'SELECT id, pwd FROM public."user" WHERE email = :email';
                $queryPrepared = $pdo->prepare($sql);
                $queryPrepared->execute(["email"=>$email]);
                $result = $queryPrepared->fetch();
                if(empty($result) || !password_verify($pwd, $result["pwd"])){
                    $errors[]="Email ou mot de passe incorrect";
                }else{
                    session_start();
                    $_SESSION["user_id"] = $result["id"];
                    header("Location: /home");
                    exit();
                }
        }
    }
        $render = new Render("login", "backoffice");
        $render->assign("errors", $errors);
        $render->render();
    }

    public function register(): void
    {
        require "../db.php";

         $errors = [];
        if($_SERVER["REQUEST_METHOD"] === "POST"
            && count($_POST)==4
            && isset($_POST["username"])
            && !empty($_POST["email"])
            && !empty($_POST["pwd"])
            && !empty($_POST["pwdConfirm"])
        ){
        $username = ucwords(strtolower(trim($_POST["username"])));
        $email = strtolower(trim($_POST["email"]));

        $errors = [];

        if(strlen($username) < 3){
            $errors[]="Le nom d'utilisateur doit faire au moins 3 caractères";
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $errors[]="Le format de l'email est invalide";
        }else{
            $sql = 'SELECT id FROM public."user" WHERE email = :email';
            $queryPrepared = $pdo->prepare($sql);
            $queryPrepared->execute(["email"=>$email]);
            $result = $queryPrepared->fetch();
            if(!empty($result)){
                $errors[]="L'email existe déjà";
            }
        }

        if(strlen($_POST["pwd"])<8 ||
            !preg_match('#[A-Z]#', $_POST["pwd"]) ||
            !preg_match('#[a-z]#', $_POST["pwd"]) ||
            !preg_match('#[0-9]#', $_POST["pwd"])
        ){
            $errors[]="Le mot de passe doit faire au moins 8 caractères avec une minuscule, une majuscule et un chiffres";
        }

        if($_POST["pwd"] != $_POST["pwdConfirm"]){
            $errors[]="Le mot de passe de confirmation ne correspond pas";
        }

        if(empty($errors))
        {
            $hashedPwd = password_hash($_POST["pwd"], PASSWORD_BCRYPT);
            $sql = 'INSERT INTO public."user" (username, email, pwd) 
                    VALUES (:username, :email, :pwd)';
            $queryPrepared = $pdo->prepare($sql);
            $queryPrepared->execute([
                "username"=>$username,
                "email"=>$email,
                "pwd"=>$hashedPwd
            ]);
            header("Location: /login");
            exit();
        }
    }
        $render = new Render("register", "backoffice");
        $render->assign("errors", $errors);
        $render->render();
    }

    public function password_reset(): void
    {
        $render = new Render("password_reset", "backoffice");
        $render->render();
    }

}