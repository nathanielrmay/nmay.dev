<?php
namespace pages\for_sale;

require_once __DIR__ . '/../aPage.php';

use lib\contracts\aPage;

abstract class aForSalePage extends aPage {
    public function getVerticalMenu() {
        return "/pages/for_sale/lib/partials/pt_vertical_menu.php";
    }
}
