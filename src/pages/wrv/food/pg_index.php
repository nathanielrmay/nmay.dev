<?php
namespace pages\wrv\food;

require_once __DIR__ . '/aFoodPage.php';

use lib\basket;
use lib\db\models\wrv\db_food_review;
use lib\db\models\wrv\db_food_review_type;

class pg_index extends aFoodPage
{
    public function getPageTitle()
    {
        return "Wilma's Food Reviews";
    }
}


$db = basket::db_web();
$reviewModel = new db_food_review($db);
$reviewTypePk = $_GET['review_type'] ?? null;
$reviews = $reviewModel->readAll($reviewTypePk);

$typeModel = new db_food_review_type($db);
$genres = $typeModel->readAll();
?>

<div style="padding: 20px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="color: #6b4a8e; margin: 0;">Food Reviews</h1>
        
        <form method="GET" action="/wrv/food/pg_index.php" style="margin: 0;">
            <select name="review_type" onchange="this.form.submit()" style="padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; background-color: #fff;">
                <option value="">All Genres</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?= htmlspecialchars($g['pk']) ?>" <?= ((string)$reviewTypePk === (string)$g['pk']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['name']) ?>
                    </option>
                <?php endforeach; ?>
                <option value="Other" <?= ($reviewTypePk === 'Other') ? 'selected' : '' ?>>Other</option>
            </select>
        </form>
    </div>
    
    <?php if (empty($reviews)): ?>
        <p style="color: #666; font-style: italic; background: #f9f9f9; padding: 30px; border-radius: 8px; text-align: center;">No reviews have been written yet. Be the first!</p>
    <?php
else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 20px;">
            <?php foreach ($reviews as $review): ?>
                <?php echo basket::render('pages/wrv/food/lib/partials/pt_review_card.php', ['review' => $review]); ?>
            <?php
    endforeach; ?>
        </div>
    <?php
endif; ?>
</div>
