<?php
namespace pages\text_games;

require_once __DIR__ . '/../aPage.php';

use lib\contracts\aPage;

abstract class aTextGamesPage extends aPage {
    public function getVerticalMenu() {
        return "/pages/text_games/lib/partials/pt_vertical_menu.php";
    }

    public function getPageTitle() {
        return "Text Games";
    }
}

