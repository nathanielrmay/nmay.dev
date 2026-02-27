<?php
namespace pages\wrv\food\food_war;

require_once __DIR__ . '/../aFoodPage.php';
use pages\wrv\food\aFoodPage;

abstract class aFoodWarPage extends aFoodPage {
    public function getVerticalMenu() {
        return "/pages/wrv/food/food_war/lib/partials/pt_vertical_menu.php";
    }
}
