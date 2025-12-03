<h2>Réinitialisation du mot de passe</h2>

<?php if ($success): ?>
    <div>
        <strong>✓ Mot de passe réinitialisé avec succès !</strong>
        <p>Votre mot de passe a été modifié. Vous pouvez maintenant vous connecter.</p>
        <a href="/login">Se connecter</a>
    </div>
<?php elseif ($showForm): ?>
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

    <form method="post" action="/reset_password?email=<?= urlencode($_GET["email"] ?? '') ?>&token=<?= urlencode($_GET["token"] ?? '') ?>">
        <input type="hidden" name="email" value="<?= htmlspecialchars($_GET["email"] ?? '') ?>">
        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET["token"] ?? '') ?>">
        
        <label for="pwd">Nouveau mot de passe :</label>
        <input type="password" id="pwd" name="pwd" required placeholder="Au moins 8 caractères avec majuscule, minuscule et chiffre"><br>

        <label for="pwdConfirm">Confirmer le mot de passe :</label>
        <input type="password" id="pwdConfirm" name="pwdConfirm" required><br>

        <button type="submit">Réinitialiser le mot de passe</button>
        <a href="/login">Retour à la connexion</a>
    </form>
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
    
    <p><a href="/login">Retour à la connexion</a></p>
<?php endif; ?>

