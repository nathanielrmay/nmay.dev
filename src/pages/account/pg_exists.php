<?php
    use lib\basket;
    use lib\db\models\web\db_users;

    if (!isset($_SESSION['user_pk'])) {
        header('Location: login.php');
        exit;
    }

    // Initialize autoloader and DB connection
    $basket = new Basket();
    $db = Basket::db_web();
    $userModel = new db_users($db);

    $pk = $_SESSION['user_pk'];
    $user = $userModel->read($pk);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($userModel->edit($_POST)) {
            $user = $userModel->read($_POST['pk']);
            echo "<p style='color: green; padding: 0 5%;'>Details updated successfully.</p>";
        } else {
            echo "<p style='color: red; padding: 0 5%;'>Error updating details.</p>";
        }
    }

    if (!$user) {
        echo "<p style='padding: 0 5%;'>User not found.</p>";
        exit;
    }
?>

<form method="post" target="_self" action="exists.php" style="padding: 5%;">
    <input type="hidden" name="pk" value="<?php echo htmlspecialchars($user['pk']); ?>">
    
    <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <label style="width: 100px; flex-shrink: 0;">user:</label>
        <input type="text" id="id" name="id" value="<?php echo htmlspecialchars($user['id']); ?>" style="flex-grow: 1;">
    </div>
    
    <div style="display: flex; align-items: center; margin-bottom: 5px;">
        <label style="width: 100px; flex-shrink: 0;">password:</label>
        <input type="password" id="password" name="password" value="" placeholder="Leave blank to keep current password" style="flex-grow: 1;">
    </div>
    
    <input type="submit" value="update" style="width: 100%; margin-top: 5px;">
</form>