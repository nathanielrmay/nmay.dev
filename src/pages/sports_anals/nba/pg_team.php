<?php
namespace pages\sports_anals\nba;

require_once __DIR__ . '/aNbaPage.php';

use lib\basket;
use lib\db\models\panal\nba\db_teams;

class pg_team extends aNbaPage {
    public function getPageTitle() {
        return "NBA Teams";
    }
}

// Fetch all teams for the list
$db = basket::db_panal();
$teamsModel = new db_teams($db);
$teams = $teamsModel->readAll();
?>

<div class="sports-nba-teams">
    <?php 
    // Use the basket render method to pass parameters cleanly
    echo basket::render('/pages/sports_anals/nba/lib/partials/teams/pt_teams.php', [
        'teams' => $teams,
        'isOpen' => true
    ]); 
    ?>

    <!-- Container for dynamic team details -->
    <div id="team-details-container" style="margin-top: 20px;">
        <?php if (isset($_GET['team_id'])): 
            $team_id = $_GET['team_id'];
            
            // Prepare tabs
            $tabs = [
                'Overview' => basket::render('/pages/sports_anals/nba/lib/partials/team/pt_team.php', [
                    'team_id' => $team_id,
                    'view' => 'overview' // We can use this flag in pt_team.php if we want to slim it down
                ]),
                'Roster' => basket::render('/pages/sports_anals/nba/lib/partials/roster/pt_roster.php', [
                    'team_id' => $team_id,
                    'isOpen' => true,
                    'style' => 'newspaper'
                ]),
                'Schedule' => basket::render('/pages/sports_anals/nba/lib/partials/schedule/pt_schedule.php', [
                    'team_id' => $team_id,
                    'isOpen' => true,
                    'style' => 'newspaper',
                    'limit' => 20
                ])
            ];

            echo basket::render('/lib/partials/tabbed_container.php', ['tabs' => $tabs]);
        ?>
        <?php else: ?>
            <div class="placeholder-message" style="text-align: center; padding: 40px; color: #666; font-style: italic;">
                Select a team from the list above to view details.
            </div>
        <?php endif; ?>
    </div>
</div>
