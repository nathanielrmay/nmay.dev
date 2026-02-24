<?php
    if (defined('LOGIN_PAGE_LOADED')) {
        return;
    }
    define('LOGIN_PAGE_LOADED', true);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    use lib\basket;
    use lib\db\models\web\db_users;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $basket = new Basket();
        $db = Basket::db_web();
        $userModel = new db_users($db);

        $user = $userModel->readByIdPassword($_POST['user'], $_POST['password']);
        if ($user) {
            $_SESSION['user_pk'] = $user['pk'];
            $_SESSION['is_admin'] = !empty($user['admin']);
            header('Location: /index.php');
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    }
?>

<?php if (isset($error)): ?>
    <p style="color: red; padding: 0 5%;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="post" target="_self" action="pg_login.php" style="padding: 5%;">
    <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <label style="width: 100px; flex-shrink: 0;">user:</label>
        <input type="text" id="user" name="user" style="flex-grow: 1;">
    </div>
    <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <label style="width: 100px; flex-shrink: 0;">password:</label>
        <input type="password" id="password" name="password" style="flex-grow: 1;">
    </div>
    <input type="submit" value="login" style="width: 100%; margin-top: 5px;"> <br>
    <div style="margin-top: 10px; text-align: center;">
        <a href="/account/pg_create.php"> create account </a>
    </div>
</form>