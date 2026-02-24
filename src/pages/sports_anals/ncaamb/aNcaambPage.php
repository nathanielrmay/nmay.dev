<?php
namespace pages\sports_anals\ncaamb;

require_once __DIR__ . '/../aSportsPage.php';
use pages\sports_anals\aSportsPage;

abstract class aNcaambPage extends aSportsPage {
    public function getVerticalMenu() {
        return "/pages/sports_anals/ncaamb/partial/pt_vertical_menu.php";
    }
}

