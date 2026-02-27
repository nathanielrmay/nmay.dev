<?php
namespace pages\wrv\food;

require_once __DIR__ . '/aFoodPage.php';

class pg_index extends aFoodPage {
    public function getPageTitle() {
        return "Wilma's Food Reviews";
    }
}
?>

<div style="padding: 20px;">
    <h1>Food Reviews</h1>
    <p>Welcome to the food review section!</p>
    
    <div style="margin-top: 30px;">
        <a href="/wrv/food/pg_create_review.php" style="display: inline-block; padding: 10px 20px; background-color: #38827e; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
            + Create New Review
        </a>
    </div>
</div>
