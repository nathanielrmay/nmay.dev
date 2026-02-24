<?php
    use lib\basket;
    use lib\db\models\web\db_users;

    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = Basket::db_web();
        $user = new db_users($db);
        $newPk = $user->write($_POST);

        if ($newPk) {
            $_SESSION['user_pk'] = $newPk;
            header('Location: exists.php');
            exit;
        } else {
            $error = 'Error creating account.';
        }
    }
?>

<?php if ($error): ?>
    <p style="color: red; padding: 0 5%;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="post" target="_self" action="pg_create.php" style="padding: 5%;">
    <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <label style="width: 100px; flex-shrink: 0;">user:</label>
        <input type="text" id="id" name="id" style="flex-grow: 1;">
    </div>
    <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <label style="width: 100px; flex-shrink: 0;">password:</label>
        <input type="password" id="password" name="password" style="flex-grow: 1;">
    </div>
    <input type="submit" value="create" style="width: 100%; margin-top: 5px;">
</form>