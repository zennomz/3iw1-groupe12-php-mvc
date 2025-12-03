<?php

namespace App\Controllers;

use App\Core\Render;

require_once __DIR__ . '/../phpMailer_config.php';

use function PHPMailer\PHPMailer\SendConfirmationMail;
use function PHPMailer\PHPMailer\SendPasswordResetMail;

class Auth
{
    public function login(): void
    {
        require "../db.php";

        session_start();

        $successMessage = $_SESSION["success_message"] ?? null;
        if (isset($_SESSION["success_message"])) {
            unset($_SESSION["success_message"]);
        }

        $errors = [];

        if (
            $_SERVER["REQUEST_METHOD"] === "POST"
            && count($_POST) == 2
            && !empty($_POST["email"])
            && !empty($_POST["pwd"])
        ) {
            $email = strtolower(trim($_POST["email"]));
            $pwd = $_POST["pwd"];

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Le format de l'email est invalide";
            } else {
                $sql = 'SELECT id, pwd, is_active FROM public."user" WHERE email = :email';
                $queryPrepared = $pdo->prepare($sql);
                $queryPrepared->execute(["email" => $email]);
                $result = $queryPrepared->fetch();
                if (empty($result) || !password_verify($pwd, $result["pwd"])) {
                    $errors[] = "Email ou mot de passe incorrect";
                } elseif (!$result["is_active"]) {
                    $errors[] = "Votre compte n'est pas encore activé. Veuillez vérifier votre email pour activer votre compte.";
                } else {
                    $_SESSION["user_id"] = $result["id"];
                    header("Location: /home");
                    exit();
                }
            }
        }
        $render = new Render("login", "backoffice");
        $render->assign("errors", $errors);
        $render->assign("successMessage", $successMessage);
        $render->render();
    }

    public function register(): void
    {
        require "../db.php";

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
                $sql = 'SELECT id FROM public."user" WHERE email = :email';
                $queryPrepared = $pdo->prepare($sql);
                $queryPrepared->execute(["email" => $email]);
                $result = $queryPrepared->fetch();
                if (!empty($result)) {
                    $errors[] = "L'email existe déjà";
                }
            }

            if (
                strlen($_POST["pwd"]) < 8 ||
                !preg_match('#[A-Z]#', $_POST["pwd"]) ||
                !preg_match('#[a-z]#', $_POST["pwd"]) ||
                !preg_match('#[0-9]#', $_POST["pwd"])
            ) {
                $errors[] = "Le mot de passe doit faire au moins 8 caractères avec une minuscule, une majuscule et un chiffres";
            }

            if ($_POST["pwd"] != $_POST["pwdConfirm"]) {
                $errors[] = "Le mot de passe de confirmation ne correspond pas";
            }

            if (empty($errors)) {
                $hashedPwd = password_hash($_POST["pwd"], PASSWORD_BCRYPT);
                $verificationToken = bin2hex(random_bytes(32));

                $sql = 'INSERT INTO public."user" (username, email, pwd, verification_token) 
                    VALUES (:username, :email, :pwd, :verification_token)';
                $queryPrepared = $pdo->prepare($sql);
                $queryPrepared->execute([
                    "username" => $username,
                    "email" => $email,
                    "pwd" => $hashedPwd,
                    "verification_token" => $verificationToken
                ]);

                SendConfirmationMail($email, $verificationToken);

                session_start();
                $_SESSION["success_message"] = "Inscription réussie ! Veuillez vérifier votre email pour activer votre compte.";

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
        require "../db.php";

        $errors = [];
        $success = false;

        if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["email"])) {
            $email = strtolower(trim($_POST["email"]));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Le format de l'email est invalide";
            } else {
                $sql = 'SELECT id, email FROM public."user" WHERE email = :email';
                $queryPrepared = $pdo->prepare($sql);
                $queryPrepared->execute(["email" => $email]);
                $result = $queryPrepared->fetch();

                if (empty($result)) {
                    $success = true;
                } else {
                    $resetToken = bin2hex(random_bytes(32));

                    $sql = 'UPDATE public."user" SET verification_token = :token WHERE email = :email';
                    $queryPrepared = $pdo->prepare($sql);
                    $queryPrepared->execute([
                        "token" => $resetToken,
                        "email" => $email
                    ]);

                    SendPasswordResetMail($email, $resetToken);
                    $success = true;
                }
            }
        }

        $render = new Render("password_reset", "backoffice");
        $render->assign("errors", $errors);
        $render->assign("success", $success);
        $render->render();
    }

    public function verify(): void
    {
        require "../db.php";

        $errors = [];
        $success = false;

        if (!empty($_GET["email"]) && !empty($_GET["token"])) {
            $email = strtolower(trim($_GET["email"]));
            $token = trim($_GET["token"]);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Le format de l'email est invalide";
            } else {
                $sql = 'SELECT id, verification_token, is_active FROM public."user" WHERE email = :email';
                $queryPrepared = $pdo->prepare($sql);
                $queryPrepared->execute(["email" => $email]);
                $result = $queryPrepared->fetch();

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
                        $sql = 'UPDATE public."user" SET is_active = true, verification_token = NULL WHERE email = :email';
                        $queryPrepared = $pdo->prepare($sql);
                        $queryPrepared->execute(["email" => $email]);

                        $success = true;
                    }
                }
            }
        } else {
            $errors[] = "Les paramètres email et token sont requis";
        }

        $render = new Render("verify", "backoffice");
        $render->assign("errors", $errors);
        $render->assign("success", $success);
        $render->render();
    }

    public function reset_password(): void
    {
        require "../db.php";

        $errors = [];
        $success = false;
        $showForm = false;

        $email = strtolower(trim($_GET["email"] ?? $_POST["email"] ?? ''));
        $token = preg_replace('/[^a-fA-F0-9]/', '', trim($_GET["token"] ?? $_POST["token"] ?? ''));

        if (!empty($email) && !empty($token)) {

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Le format de l'email est invalide";
            } else {
                $sql = 'SELECT id, verification_token FROM public."user" WHERE email = :email';
                $queryPrepared = $pdo->prepare($sql);
                $queryPrepared->execute(["email" => $email]);
                $result = $queryPrepared->fetch();

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
                                $hashedPwd = password_hash($_POST["pwd"], PASSWORD_BCRYPT);
                                $sql = 'UPDATE public."user" SET pwd = :pwd, verification_token = NULL WHERE email = :email';
                                $queryPrepared = $pdo->prepare($sql);
                                $queryPrepared->execute([
                                    "pwd" => $hashedPwd,
                                    "email" => $email
                                ]);

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

        $render = new Render("reset_password", "backoffice");
        $render->assign("errors", $errors);
        $render->assign("success", $success);
        $render->assign("showForm", $showForm);
        $render->render();
    }
}
