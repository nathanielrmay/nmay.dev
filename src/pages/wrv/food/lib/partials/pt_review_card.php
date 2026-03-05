<?php
/**
 * Display a review card for the index page.
 * Calculates dynamic overall rating using food_basket.
 * 
 * Input Arguments:
 * $args['review'] - An associative array containing review data joined with generic place info.
 */
namespace pages\wrv\food\lib\partials;

use pages\wrv\food\lib\food_basket;

$review = $args['review'] ?? null;
if (!$review) return;

// Figure out what 'type' or genre the place is
$genre = $review['genre_name'] ?? '';

// Fallback to google places types if no genre is set
if (empty($genre)) {
    $types = isset($review['types']) ? json_decode($review['types'], true) : [];
    if (!empty($types) && is_array($types)) {
        $filtered = array_diff($types, ['restaurant', 'food', 'point_of_interest', 'establishment']);
        if (!empty($filtered)) {
            $genreStr = reset($filtered);
            $genreStr = str_replace('_', ' ', $genreStr);
            $genre = ucwords($genreStr);
        }
    }
}

// Dynamically compute the overall score
$overallRating = food_basket::calculateOverallRating($review);

?>

<a href="/wrv/food/pg_review.php?pk=<?= (int)$review['pk'] ?>" style="text-decoration: none; color: inherit; display: block; background-color: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); transition: transform 0.2s, box-shadow 0.2s; position: relative;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 15px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.05)';">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 5px;">
        <div style="flex-grow: 1; padding-right: 15px;">
            <h2 style="margin: 0 0 5px 0; color: #333; font-size: 1.4rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($review['title']) ?></h2>
            <div style="color: #666; font-size: 0.95rem; margin-bottom: 10px;">
                <strong style="color: #6b4a8e; font-size: 1.1rem;"><?= htmlspecialchars($review['place_name'] ?? 'Unknown Place') ?></strong>
                <?php if ($genre): ?>
                    &nbsp;&bull;&nbsp; <span style="background: #f0f0f0; padding: 2px 8px; border-radius: 12px; font-size: 0.85em;"><?= htmlspecialchars($genre) ?></span>
                <?php endif; ?>
            </div>
            <?php if (!empty($review['date_visited'])): ?>
                <div style="color: #999; font-size: 0.85rem;">Visited on <?= date('M j, Y', strtotime($review['date_visited'])) ?></div>
            <?php endif; ?>
        </div>
        
        <?php if ($overallRating !== null): ?>
            <div style="background-color: #f8f9fa; border: 2px solid <?= $overallRating >= 7.5 ? '#4caf50' : ($overallRating >= 5 ? '#ff9800' : '#f44336') ?>; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; flex-direction: column; flex-shrink: 0; margin-left: auto;">
                <span style="font-weight: bold; font-size: 1.3rem; color: #333; line-height: 1;"><?= number_format($overallRating, 1) ?></span>
            </div>
        <?php endif; ?>
    </div>
    
    <div style="margin-top: 15px; color: #3498db; font-weight: bold; font-size: 0.9rem; text-align: right;">
        Read Full Review &rarr;
    </div>
</a>
