<?php
namespace pages\the_league;
use lib\contracts\aPage;

abstract class aLeaguePage extends aPage
{
    public function __construct()
    {
        $this->addCss('/pages/the_league/lib/the_league.css');
    }

    public function getVerticalMenu()
    {
        return "/pages/the_league/lib/partials/pt_vertical_menu.php";
    }

    public function getHeader()
    {
        return "/pages/the_league/lib/partials/pt_header.php";
    }

    public function getBodyClass()
    {
        return "the-league";
    }
}
