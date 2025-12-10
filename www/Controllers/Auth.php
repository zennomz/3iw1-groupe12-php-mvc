<?php

namespace App\Controllers;

use App\Core\Render;
use App\Core\Database;
use App\Models\UserModel;

require_once __DIR__ . '/../phpMailer_config.php';

use function PHPMailer\PHPMailer\SendConfirmationMail;
use function PHPMailer\PHPMailer\SendPasswordResetMail;

class Auth
{
    public function login(): void
    {
        $pdo = Database::getConnection();
        $userModel = new UserModel($pdo);

        $successMessage = $_SESSION['success_message'] ?? null;
        unset($_SESSION['success_message']);

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
                $user = $userModel->getUserByEmail($email);

                if(empty($user) || !password_verify($pwd, $user["pwd"])){
                    $errors[]="Email ou mot de passe incorrect";
                }elseif(!$user["is_active"]){
                    $errors[]="Compte non activé. Veuillez vérifier votre email pour activer votre compte.";
                }else{
                    session_start();
                    $_SESSION["user_id"] = $user["id"];
                    $_SESSION["role"] = $user["role"];
                    header("Location: /home");
                    exit();
                }
        }
    }
        $render = new Render("login", "authentification");
        $render->assign("errors", $errors);
        $render->assign("successMessage", $successMessage);
        $render->render();
    }

    public function register(): void
    {
        $pdo = Database::getConnection();
        $userModel = new UserModel($pdo);

        $errors = [];
        if (
            $_SERVER["REQUEST_METHOD"] === "POST"
            && count($_POST) == 4
            && isset($_POST["username"])
            && !empty($_POST["email"])
            && !empty($_POST["pwd"])
            && !empty($_POST["pwdConfirm"])
        ) {
            $username = ucwords(strtolower(trim($_POST["username"])));
            $email = strtolower(trim($_POST["email"]));

            $errors = [];

            if (strlen($username) < 3) {
                $errors[] = "Le nom d'utilisateur doit faire au moins 3 caractères";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Le format de l'email est invalide";
            } else {
                $result = $userModel->getUserByEmail($email);
                if (!empty($result)) {
                    $errors[] = "Un compte avec cet email existe déjà";
                }
            }

            if (
                strlen($_POST["pwd"]) < 8 ||
                !preg_match('#[A-Z]#', $_POST["pwd"]) ||
                !preg_match('#[a-z]#', $_POST["pwd"]) ||
                !preg_match('#[0-9]#', $_POST["pwd"]) ||
                !preg_match('#[^a-zA-Z0-9_-]#', $_POST["pwd"])
                ){
                $errors[] = "Le mot de passe doit faire au moins 8 caractères avec une minuscule, une majuscule, un chiffre et un caractère spécial.";
            }

            if ($_POST["pwd"] != $_POST["pwdConfirm"]) {
                $errors[] = "Le mot de passe de confirmation ne correspond pas";
            }

            if (empty($errors)) {
                $hashedPwd = password_hash($_POST["pwd"], PASSWORD_BCRYPT);
                $userModel->createUser($username, $email, $hashedPwd);
                $verificationToken = bin2hex(random_bytes(32));
                $userModel->setVerificationToken($email, $verificationToken);

                SendConfirmationMail($email, $verificationToken);

                session_start();
                $_SESSION["success_message"] = "Inscription réussie ! Veuillez vérifier votre email pour activer votre compte.";

                header("Location: /login");
                exit();
            }
        }
        $render = new Render("register", "authentification");
        $render->assign("errors", $errors);
        $render->render();
    }

    public function password_reset(): void
    {
        $pdo = Database::getConnection();
        $userModel = new UserModel($pdo);

        $errors = [];
        $success = false;

        if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["email"])) {
            $email = strtolower(trim($_POST["email"]));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Le format de l'email est invalide";
            } else {
                $result = $userModel->getUserByEmail($email);

                if (empty($result)) {
                    $success = true;
                } else {
                    $resetToken = bin2hex(random_bytes(32));

                    $userModel->setVerificationToken($email, $resetToken);
                    SendPasswordResetMail($email, $resetToken);
                    $success = true;
                }
            }
        }

        $render = new Render("password_reset", "authentification");
        $render->assign("errors", $errors);
        $render->assign("success", $success);
        $render->render();
    }

    public function verify(): void
    {
        $pdo = Database::getConnection();
        $userModel = new UserModel($pdo);

        $errors = [];
        $success = false;

        if (!empty($_GET["email"]) && !empty($_GET["token"])) {
            $email = strtolower(trim($_GET["email"]));
            $token = trim($_GET["token"]);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Le format de l'email est invalide";
            } else {
                $result = $userModel->getUserByEmail($email);

                if (empty($result)) {
                    $errors[] = "Aucun compte trouvé avec cet email";
                } else {

                    $dbToken = preg_replace('/[^a-fA-F0-9]/', '', $result["verification_token"] ?? '');
                    $token = preg_replace('/[^a-fA-F0-9]/', '', $token);

                    if ($dbToken !== $token || empty($dbToken) || empty($token)) {
                        $errors[] = "Le token de vérification est invalide";
                    } elseif ($result["is_active"]) {
                        $errors[] = "Ce compte est déjà activé";
                    } else {
                        $userModel->activateUser($email);
                        $success = true;
                    }
                }
            }
        } else {
            $errors[] = "Les paramètres email et token sont requis";
        }

        $render = new Render("verify", "authentification");
        $render->assign("errors", $errors);
        $render->assign("success", $success);
        $render->render();
    }

    public function reset_password(): void
    {
        $pdo = Database::getConnection();
        $userModel = new UserModel($pdo);

        $errors = [];
        $success = false;
        $showForm = false;

        $email = strtolower(trim($_GET["email"] ?? $_POST["email"] ?? ''));
        $token = preg_replace('/[^a-fA-F0-9]/', '', trim($_GET["token"] ?? $_POST["token"] ?? ''));

        if (!empty($email) && !empty($token)) {

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Le format de l'email est invalide";
            } else {
                $result = $userModel->getUserByEmail($email);

                if (empty($result)) {
                    $errors[] = "Aucun compte trouvé avec cet email";
                } else {
                    $dbToken = preg_replace('/[^a-fA-F0-9]/', '', $result["verification_token"] ?? '');

                    if ($dbToken !== $token || empty($dbToken) || empty($token)) {
                        $errors[] = "Le token de réinitialisation est invalide ou a expiré";
                    } else {
                        $showForm = true;

                        if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["pwd"]) && !empty($_POST["pwdConfirm"])) {
                            
                            $postEmail = strtolower(trim($_POST["email"] ?? ''));
                            $postToken = preg_replace('/[^a-fA-F0-9]/', '', trim($_POST["token"] ?? ''));

                            if ($postEmail !== $email || $postToken !== $token) {
                                $errors[] = "Les paramètres de réinitialisation sont invalides";
                            } elseif (strlen($_POST["pwd"]) < 8 ||
                                !preg_match('#[A-Z]#', $_POST["pwd"]) ||
                                !preg_match('#[a-z]#', $_POST["pwd"]) ||
                                !preg_match('#[0-9]#', $_POST["pwd"])
                            ) {
                                $errors[] = "Le mot de passe doit faire au moins 8 caractères avec une minuscule, une majuscule et un chiffre";
                            } elseif ($_POST["pwd"] != $_POST["pwdConfirm"]) {
                                $errors[] = "Le mot de passe de confirmation ne correspond pas";
                            } else {
                                $userModel->resetPassword($email, password_hash($_POST["pwd"], PASSWORD_BCRYPT));

                                $success = true;
                                $showForm = false;

                                session_start();
                                $_SESSION["success_message"] = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";

                                header("Location: /login");
                                exit();
                            }
                        }
                    }
                }
            }
        } else {
            $errors[] = "Les paramètres email et token sont requis";
        }

        $render = new Render("reset_password", "authentification");
        $render->assign("errors", $errors);
        $render->assign("success", $success);
        $render->assign("showForm", $showForm);
        $render->render();
    }

    public function logout(): void
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: /login");
        exit();
    }
}
