<?php
namespace pages\wrv\food;

require_once __DIR__ . '/aFoodPage.php';

use lib\basket;

class pg_idc_wars extends aFoodPage {
    public function getPageTitle() {
        return "I Don't Care Wars - Setup";
    }
}
?>

<div style="padding: 20px; max-width: 800px;">
    <h2>I Don't Care (IDC) Wars ⚔️🍔</h2>
    <p>For when nobody can make up their mind on where to eat. Set up a tournament, build a list of restaurants, and let the games decide!</p>
    
    <div style="background-color: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px;">
        <h3>1. Tournament Format</h3>
        <form method="POST" action="" style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label style="font-weight: bold; display: block; margin-bottom: 5px;">Select Format:</label>
                <select name="tournament_format" style="padding: 8px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; width: 100%; max-width: 300px;">
                    <option value="bracket">Tournament Bracket</option>
                    <option value="round_robin">Round Robin</option>
                    <option value="ranked_choice">Ranked Choice Voting (One Round)</option>
                </select>
            </div>
            
            <!-- In the future, this button will save the tournament to the database and redirect to a management page -->
            <button type="submit" name="start_setup" style="padding: 10px 20px; background-color: #38827e; color: white; border: none; border-radius: 4px; font-size: 1rem; font-weight: bold; cursor: pointer; width: fit-content;">
                Start Setup
            </button>
        </form>
    </div>

    <div style="background-color: #fafafa; padding: 20px; border-radius: 8px; border: 1px solid #ccc; margin-bottom: 20px; opacity: 0.6;">
        <h3>2. Add Restaurants (Coming Soon)</h3>
        <p>Once a tournament is started, you'll be able to search and add restaurants here.</p>
        <p><em>Search parameters will include: Name, Location (Proximity/Radius), and Cuisine type (Genre).</em></p>
    </div>
</div>
