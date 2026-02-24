<?php
namespace pages\about;

use lib\contracts\aPage;

abstract class aAboutPage extends aPage {

    public function getPageTitle(): string
    {
        return "about me";
    }
}
