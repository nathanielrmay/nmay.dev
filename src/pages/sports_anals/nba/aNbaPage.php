<?php
namespace pages\sports_anals\nba;

require_once __DIR__ . '/../aSportsPage.php';
use pages\sports_anals\aSportsPage;

abstract class aNbaPage extends aSportsPage {
    public function getVerticalMenu() {
        return "/pages/sports_anals/nba/lib/partials/pt_vertical_menu.php";
    }

    public function getFooter() {
        return "/pages/sports_anals/nba/lib/partials/pt_footer.php";
    }
}
