<h2>Réinitialisation du mot de passe</h2>

<?php if ($success): ?>
    <div>
        <strong>✓ Email envoyé !</strong>
        <p>Si un compte existe avec cet email, vous recevrez un lien pour réinitialiser votre mot de passe.</p>
        <a href="/login">Retour à la connexion</a>
    </div>
<?php else: ?>
    <?php if (!empty($errors)): ?>
        <div>
            <strong>Erreurs :</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?= $_POST["email"] ?? "" ?>" required><br>

        <button type="submit">Réinitialiser le mot de passe</button>
        <a href="/login">Retour à la connexion</a>
    </form>
<?php endif; ?>