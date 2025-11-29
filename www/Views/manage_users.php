<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Manage Users</h2>
    <a href="/admin">Back to Admin Dashboard</a>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Actif</th>
                <th>Date de création</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <form method="post">
                        <td><?= $user['id'] ?></td>
                        <td>
                            <input type="text" name="username" value="<?= $user['username'] ?>" required>
                        </td>
                        <td><?= $user['email'] ?></td>
                        <td>
                            <input type="checkbox" name="is_active" <?= $user['is_active'] ? 'checked' : '' ?>>
                        </td>
                        <td><?= $user['date_created'] ?></td>
                        <td>
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <input type="hidden" name="action" value="update">
                            <input type="submit" value="Update">
                    </form>
                    <form method="post">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit">Delete</button>
                    </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>