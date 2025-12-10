<?php

namespace App\Models;

use function PHPMailer\PHPMailer\SendConfirmationMail;

class UserModel
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createUser(string $username, string $email, string $hashedPwd)
    {
        $sql = 'INSERT INTO public."user" (username, email, pwd, is_active, date_created) VALUES (:username, :email, :pwd, FALSE, NOW())';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'username' => $username,
            'email' => $email,
            'pwd' => $hashedPwd
        ]);
    }

    public function getUserByEmail(string $email)
    {
        $sql = 'SELECT id, username, email, pwd, role, is_active, verification_token FROM public."user" WHERE email = :email';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function setVerificationToken(string $email, string $token)
    {
        $sql = 'UPDATE public."user" SET verification_token = :token WHERE email = :email';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'token' => $token,
            'email' => $email
        ]);
    }

    public function getUserForToken(string $email, string $token)
    {
        $sql = 'SELECT id, username, email, pwd, is_active FROM public."user" WHERE email = :email';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            return null;
        }

        if ($user['verification_token'] !== $token) {
            return null;
        }

        return $user;
    }

    public function activateUser(string $email)
    {
        $sql = 'UPDATE public."user" SET is_active = TRUE, verification_token = NULL WHERE email = :email';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['email' => $email]);
    }

    public function resetPassword(string $email, string $hashedPwd)
    {
        $sql = 'UPDATE public."user" SET pwd = :pwd, verification_token = NULL WHERE email = :email';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'pwd' => $hashedPwd,
            'email' => $email
        ]);
    }

    public function getAllUsers()
    {
        $sql = 'SELECT id, username, email, role, is_active, date_created FROM public."user" ORDER BY id ASC';
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateUser(int $userId, string $username, bool $isActive)
    {
        $sql = 'UPDATE public."user" SET username = :username, is_active = :is_active WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'username' => $username,
            'is_active' => $isActive ? 1 : 0,
            'id' => $userId
        ]);
    }

    public function deleteUser(int $userId)
    {
        $sql = 'DELETE FROM public."user" WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }
}