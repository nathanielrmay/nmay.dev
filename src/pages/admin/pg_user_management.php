<?php
namespace pages\admin;

require_once __DIR__ . '/aAdminPage.php';

use lib\basket;
use lib\db\models\web\db_users;
use Exception;

class pg_user_management extends aAdminPage {
    public function getPageTitle() {
        return "User Management";
    }
}

$db = basket::db_web();
$usersModel = new db_users($db);

$message = '';
$error = '';
$editUser = null;

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Create / Main Edit Form Action
        if (isset($_POST['action']) && ($_POST['action'] === 'create' || $_POST['action'] === 'edit')) {
            $isEdit = $_POST['action'] === 'edit';
            $id = $_POST['id'];
            $password = $_POST['password'];
            $disabled = isset($_POST['disabled']);
            
            if ($isEdit) {
                $pk = (int)$_POST['pk'];
                if ($usersModel->edit(['pk' => $pk, 'id' => $id, 'password' => $password, 'disabled' => $disabled])) {
                    $message = "User updated successfully.";
                } else {
                    $error = "Failed to update user.";
                }
            } else {
                if ($usersModel->write(['id' => $id, 'password' => $password, 'disabled' => $disabled])) {
                    $message = "User created successfully.";
                } else {
                    $error = "Failed to create user.";
                }
            }
        } 
        // 2. Table Actions (Delete)
        elseif (isset($_POST['delete_pk'])) {
            $pkToDelete = (int)$_POST['delete_pk'];
            if ($usersModel->delete($pkToDelete)) {
                $message = "User deleted successfully.";
            } else {
                $error = "Failed to delete user.";
            }
        } 
        // 3. Table Actions (Submit/Update Row)
        elseif (isset($_POST['update_pk'])) {
            $pkToUpdate = (int)$_POST['update_pk'];
            $isDisabled = isset($_POST['disabled_status'][$pkToUpdate]);
            
            $currentUser = $usersModel->read($pkToUpdate);
            if ($currentUser) {
                $updateData = [
                    'pk' => $pkToUpdate,
                    'id' => $currentUser['id'],
                    'password' => '', // Don't change password
                    'disabled' => $isDisabled
                ];
                
                if ($usersModel->edit($updateData)) {
                    $message = "User updated (disabled status saved).";
                    $editUser = $usersModel->read($pkToUpdate); // Load into edit form
                } else {
                    $error = "Failed to update user status.";
                }
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

$users = $usersModel->readAll();
?>

<div class="admin-users-container" style="padding: 20px;">
    <h1>User Management</h1>
    
    <?php if ($message): ?>
        <div class="alert success" style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 20px; background-color: #f0fff0;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert error" style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 20px; background-color: #fff0f0;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Collapsible Form Section -->
    <details <?= $editUser ? 'open' : '' ?> style="margin-bottom: 30px; border: 1px solid #ddd; background: #f9f9f9; border-radius: 5px;">
        <summary style="padding: 15px; cursor: pointer; font-weight: bold; outline: none;">
            <?= $editUser ? 'Edit User: ' . htmlspecialchars($editUser['id']) : 'Create New User' ?>
        </summary>
        
        <div class="user-form-content" style="padding: 20px; border-top: 1px solid #ddd;">
            <form method="POST" action="">
                <input type="hidden" name="action" value="<?= $editUser ? 'edit' : 'create' ?>">
                <?php if ($editUser): ?>
                    <input type="hidden" name="pk" value="<?= $editUser['pk'] ?>">
                <?php endif; ?>
                
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 5px; font-weight: bold;">ID / Username:</label>
                    <input type="text" name="id" value="<?= $editUser ? htmlspecialchars($editUser['id']) : '' ?>" required style="padding: 5px; width: 300px;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 5px; font-weight: bold;">Password:</label>
                    <input type="password" name="password" <?= $editUser ? '' : 'required' ?> placeholder="<?= $editUser ? 'Leave blank to keep current' : '' ?>" style="padding: 5px; width: 300px;">
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold;">
                        <input type="checkbox" name="disabled" <?= ($editUser && isset($editUser['disabled']) && $editUser['disabled']) ? 'checked' : '' ?>>
                        Disabled
                    </label>
                </div>
                
                <button type="submit" style="padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;">
                    <?= $editUser ? 'Update User' : 'Create User' ?>
                </button>
                <?php if ($editUser): ?>
                    <a href="?" style="margin-left: 10px; color: #666; text-decoration: none;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </details>

    <!-- Users List Form -->
    <form method="POST" action="">
        <table class="user-list-table" border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; background: white;">
            <thead>
                <tr style="background: #eee; text-align: left;">
                    <th>pk</th>
                    <th>id</th>
                    <th>admin</th>
                    <th>disabled</th>
                    <th>Delete</th>
                    <th>Submit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['pk']) ?></td>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= (isset($user['admin']) && $user['admin']) ? 'Yes' : 'No' ?></td>
                        <td>
                            <input type="checkbox" name="disabled_status[<?= $user['pk'] ?>]" value="1" <?= (isset($user['disabled']) && $user['disabled']) ? 'checked' : '' ?>>
                        </td>
                        <td>
                            <button type="submit" name="delete_pk" value="<?= $user['pk'] ?>" style="cursor: pointer; padding: 5px 10px; background-color: #dc3545; color: white; border: none; border-radius: 3px;" onclick="return confirm('Are you sure you want to delete user &quot;<?= htmlspecialchars($user['id']) ?>&quot;?');">
                                Delete
                            </button>
                        </td>
                        <td>
                            <button type="submit" name="update_pk" value="<?= $user['pk'] ?>" style="cursor: pointer; padding: 5px 10px; background-color: #28a745; color: white; border: none; border-radius: 3px;">
                                Submit
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</div>