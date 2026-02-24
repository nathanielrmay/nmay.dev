<?php
namespace pages\sports_anals;
use lib\contracts\aPage;

abstract class aSportsPage extends aPage {
    public function __construct()
    {
        $this->addCss('/pages/sports_anals/lib/newspaper.css');
    }

    public function getVerticalMenu()
    {
        return "/pages/sports_anals/lib/partials/pt_vertical_menu.php";
    }

    public function getHeader()
    {
        return "/pages/sports_anals/lib/partials/pt_header.php";
    }

    public function getBodyClass()
    {
        return "newspapercss";
    }
}
