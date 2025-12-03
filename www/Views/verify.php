<h2>Vérification de votre compte</h2>

<?php if ($success): ?>
    <div>
        <strong>✓ Compte activé avec succès !</strong>
        <p>Votre compte a été vérifié et activé. Vous pouvez maintenant vous connecter.</p>
        <a href="/login">Se connecter</a>
    </div>
<?php else: ?>
    <?php if (!empty($errors)): ?>
        <div>
            <strong>✗ Erreur lors de la vérification</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <p><a href="/login">Retour à la page de connexion</a></p>
<?php endif; ?>

