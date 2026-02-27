<?php
namespace pages\wrv;
use lib\contracts\aPage;

abstract class aWrvPage extends aPage {
    public function __construct()
    {
        // Add WRV-specific CSS
        $this->addCss('/pages/wrv/lib/wrv.css');
    }

    public function getVerticalMenu()
    {
        return "/pages/wrv/lib/partials/pt_vertical_menu.php";
    }

    public function getHeader()
    {
        return "/pages/wrv/lib/partials/pt_header.php";
    }

    public function getBodyClass()
    {
        return "wrvcss";
    }
}
