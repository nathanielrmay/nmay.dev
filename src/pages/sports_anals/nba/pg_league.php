<?php
namespace pages\sports_anals\nba;

require_once __DIR__ . '/aNbaPage.php';

use lib\basket;
use lib\db\models\panal\nba\db_standings;
use lib\db\models\panal\nba\db_schedule;
use pages\sports_anals\nba\aNbaPage;

class pg_league extends aNbaPage {
    public function getPageTitle() {
        return "NBA Analysis Dashboard";
    }
}

$db = basket::db_panal();
$standingsModel = new db_standings($db);
$standings = $standingsModel->readAll();

$tabs = [
    'Schedule' => basket::render('/pages/sports_anals/nba/lib/partials/schedule/pt_schedule.php', [ 'showGameType' => false ]),
    'Standings' => basket::render('/pages/sports_anals/nba/lib/partials/standings/pt_standings.php', [ 'standings' => $standings ]),
    'News' => basket::render('/pages/sports_anals/nba/lib/partials/news/pt_news.php', [ 'columns' => 1, 'isOpen' => true, 'fontSizeScale' => 0.85 ])
];
?>
<div class="content_root">
    <?= basket::render('/lib/partials/tabbed_container.php', ['tabs' => $tabs]) ?>
</div>

<style>
    .content_root {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
</style>
