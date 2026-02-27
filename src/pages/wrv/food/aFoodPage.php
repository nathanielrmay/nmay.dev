<?php
namespace pages\wrv\food;

require_once __DIR__ . '/../aWrvPage.php';
use pages\wrv\aWrvPage;

abstract class aFoodPage extends aWrvPage {
    public function getVerticalMenu() {
        return "/pages/wrv/food/lib/partials/pt_vertical_menu.php";
    }
}
