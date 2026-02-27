<?php
namespace pages\wrv\food\idc_war;

require_once __DIR__ . '/../aFoodPage.php';
use pages\wrv\food\aFoodPage;

abstract class aIdcWarPage extends aFoodPage {
    public function getVerticalMenu() {
        return "/pages/wrv/food/idc_war/lib/partials/pt_vertical_menu.php";
    }
}
