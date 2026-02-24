<?php
namespace pages\admin;

use lib\contracts\aPage;

abstract class aAdminPage extends aPage {
    public function getVerticalMenu() {
        return "/pages/admin/lib/partials/pt_vertical_menu.php";
    }
}
