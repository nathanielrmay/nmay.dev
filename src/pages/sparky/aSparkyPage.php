<?php
namespace pages\sparky;
use lib\contracts\aPage;

abstract class aSparkyPage extends aPage {
    public function getBodyClass()
    {
        return "sparky";
    }
}
