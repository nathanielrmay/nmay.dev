<?php
namespace pages\wrv\food;

require_once __DIR__ . '/aFoodPage.php';

use lib\basket;
use lib\db\models\wrv\db_food_review;

class pg_index extends aFoodPage {
    public function getPageTitle() {
        return "Wilma's Food Reviews";
    }
}

$db = basket::db_web();
$reviewModel = new db_food_review($db);
$reviews = $reviewModel->readAll();
?>

<div style="padding: 20px; max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="color: #6b4a8e; margin: 0;">Food Reviews</h1>
        <a href="/wrv/food/pg_create_review.php" style="display: inline-block; padding: 10px 20px; background-color: #38827e; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.2s;">
            + Write Review
        </a>
    </div>
    
    <?php if (empty($reviews)): ?>
        <p style="color: #666; font-style: italic; background: #f9f9f9; padding: 30px; border-radius: 8px; text-align: center;">No reviews have been written yet. Be the first!</p>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($reviews as $review): 
                $types = isset($review['types']) ? json_decode($review['types'], true) : [];
                $genre = '';
                if (!empty($types) && is_array($types)) {
                    // Filter out generic types to find a good genre
                    $filtered = array_diff($types, ['restaurant', 'food', 'point_of_interest', 'establishment']);
                    if (!empty($filtered)) {
                        $genreStr = reset($filtered);
                        $genreStr = str_replace('_', ' ', $genreStr);
                        $genre = ucwords($genreStr);
                    }
                }
            ?>
                <div style="background-color: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                        <div>
                            <h2 style="margin: 0 0 5px 0; color: #333; font-size: 1.5rem;"><?= htmlspecialchars($review['title']) ?></h2>
                            <div style="color: #666; font-size: 0.95rem; margin-bottom: 5px;">
                                <strong style="color: #6b4a8e; font-size: 1.1rem;"><?= htmlspecialchars($review['place_name']) ?></strong>
                                <?php if ($genre): ?>
                                    &nbsp;&bull;&nbsp; <span style="background: #f0f0f0; padding: 2px 8px; border-radius: 12px; font-size: 0.85em;"><?= htmlspecialchars($genre) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($review['date_visited'])): ?>
                                <div style="color: #999; font-size: 0.85rem;">Visited on <?= date('M j, Y', strtotime($review['date_visited'])) ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (isset($review['rating_overall'])): ?>
                            <div style="background-color: #f8f9fa; border: 2px solid <?= $review['rating_overall'] >= 7 ? '#4caf50' : ($review['rating_overall'] >= 4 ? '#ff9800' : '#f44336') ?>; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; flex-direction: column; flex-shrink: 0;">
                                <span style="font-weight: bold; font-size: 1.4rem; color: #333; line-height: 1;"><?= number_format($review['rating_overall'], 1) ?></span>
                                <span style="font-size: 0.65rem; color: #666; font-weight: bold; text-transform: uppercase; margin-top: 2px;">Overall</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="color: #444; line-height: 1.6; padding-top: 15px; border-top: 1px solid #eee; margin-bottom: 15px; white-space: pre-wrap;"><?= htmlspecialchars($review['review_text']) ?></div>
                    
                    <?php if (isset($review['rating_product']) || isset($review['rating_value'])): ?>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; background: #fdfdfd; padding: 15px; border-radius: 6px; border: 1px solid #f0f0f0;">
                        <?php if (isset($review['rating_product'])): ?>
                            <div style="text-align: center; flex: 1; min-width: 80px;">
                                <div style="font-size: 0.8rem; color: #666; text-transform: uppercase;">Product</div>
                                <div style="font-weight: bold; font-size: 1.1rem; color: #333;"><?= number_format($review['rating_product'], 1) ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($review['rating_service'])): ?>
                            <div style="text-align: center; flex: 1; min-width: 80px; border-left: 1px solid #eee;">
                                <div style="font-size: 0.8rem; color: #666; text-transform: uppercase;">Service</div>
                                <div style="font-weight: bold; font-size: 1.1rem; color: #333;"><?= number_format($review['rating_service'], 1) ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($review['rating_atmosphere'])): ?>
                            <div style="text-align: center; flex: 1; min-width: 80px; border-left: 1px solid #eee;">
                                <div style="font-size: 0.8rem; color: #666; text-transform: uppercase;">Atmosphere</div>
                                <div style="font-weight: bold; font-size: 1.1rem; color: #333;"><?= number_format($review['rating_atmosphere'], 1) ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($review['rating_value'])): ?>
                            <div style="text-align: center; flex: 1; min-width: 80px; border-left: 1px solid #eee;">
                                <div style="font-size: 0.8rem; color: #666; text-transform: uppercase;">Value</div>
                                <div style="font-weight: bold; font-size: 1.1rem; color: #333;"><?= number_format($review['rating_value'], 1) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
