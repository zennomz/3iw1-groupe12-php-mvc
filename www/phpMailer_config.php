<?php

namespace PHPMailer\PHPMailer;

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

function SendConfirmationMail($sendTo, $token)
{

    $mail = new PHPMailer(true);

    try {
        $mail->CharSet = 'UTF-8';

        $mail->isSMTP();

        $mail->Host = "mailhog";
        $mail->Port = "1025";
        $mail->SMTPAuth = false;

        $link = "http://localhost:8081/verify?email=" . urlencode($sendTo) . "&token=" . urlencode($token);
        $body = "Bonjour,<br>Veuillez confirmer votre inscription en cliquant ici : <a href='$link'>Confirmer mon compte</a>";

        $mail->setFrom("admin@example.com", "Projet MVC from Scratch");
        $mail->Subject = 'Confirmation de votre inscription';
        $mail->addAddress($sendTo);
        $mail->Body = $body;
        $mail->isHTML(true);

        $mail->send();

        return "Succes : Mail envoyé";

    } catch (PHPMailerException $e) {

        return "Erreur :" . $e->getMessage();

    }
}

function SendPasswordResetMail($sendTo, $token)
{

    $mail = new PHPMailer(true);

    try {
        $mail->CharSet = 'UTF-8';

        $mail->isSMTP();

        $mail->Host = "mailhog";
        $mail->Port = "1025";
        $mail->SMTPAuth = false;

        $link = "http://localhost:8081/reset_password?email=" . urlencode($sendTo) . "&token=" . urlencode($token);
        $body = "Bonjour,<br>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le lien suivant pour créer un nouveau mot de passe : <a href='$link'>Réinitialiser mon mot de passe</a><br><br>Si vous n'avez pas fait cette demande, ignorez cet email.";

        $mail->setFrom("admin@example.com", "Projet MVC from Scratch");
        $mail->Subject = 'Réinitialisation de votre mot de passe';
        $mail->addAddress($sendTo);
        $mail->Body = $body;
        $mail->isHTML(true);

        $mail->send();

        return "Succes : Mail envoyé";

    } catch (PHPMailerException $e) {

        return "Erreur :" . $e->getMessage();

    }
}
