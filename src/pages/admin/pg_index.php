<?php
namespace pages\admin;

require_once __DIR__ . '/aAdminPage.php';

class pg_index extends aAdminPage {

    public function getPageTitle() {
        return "Admin Dashboard";
    }
}
?>
<div class="admin-dashboard">
    <h1>Admin Dashboard</h1>
    <p>Welcome to the administration area.</p>
</div>
