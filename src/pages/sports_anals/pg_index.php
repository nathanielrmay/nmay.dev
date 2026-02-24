<?php
namespace pages\sports_anals;

require_once __DIR__ . '/aSportsPage.php';

class pg_index extends aSportsPage {
    public function getPageTitle() {
        return "Sports Analysis Dashboard";
    }
}
?>