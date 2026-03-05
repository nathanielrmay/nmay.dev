<?php
namespace pages\wrv\food;

require_once __DIR__ . '/aFoodPage.php';

use lib\basket;
use lib\db\models\wrv\db_food_review;
use pages\wrv\food\lib\food_basket;

class pg_review extends aFoodPage {
    public function getPageTitle() {
        return "Food Review - Wilma's Reviews";
    }
}

$pk = $_GET['pk'] ?? null;
if (!$pk || !is_numeric($pk)) {
    header("Location: /wrv/food/pg_index.php");
    exit;
}

$db = basket::db_web();
$reviewModel = new db_food_review($db);
$review = $reviewModel->readById((int)$pk);

if (!$review) {
    header("Location: /wrv/food/pg_index.php");
    exit;
}

// Fetch the full generic rating row
$sql = "
    SELECT fr.*, p.name as place_name, 
           frr.rating_product, frr.rating_value, frr.rating_service, frr.rating_atmosphere,
           c.types, c.formatted_address, c.price_level, c.phone_number, c.website,
           frt.name as genre_name
    FROM web.wrv.food_review fr
    JOIN web.wrv.places_place p ON fr.fk_places_place = p.pk
    LEFT JOIN web.wrv.food_review_rating frr ON fr.pk = frr.fk_food_review
    LEFT JOIN web.wrv.places_cache c ON p.pk = c.fk_places_place
    LEFT JOIN web.wrv.food_review_type frt ON fr.fk_review_type = frt.pk
    WHERE fr.pk = :pk
";
$stmt = $db->prepare($sql);
$stmt->execute(['pk' => $pk]);
$fullReview = $stmt->fetch(\PDO::FETCH_ASSOC);

if (!$fullReview) {
    echo "Review details could not be loaded.";
    exit;
}

$overallRating = food_basket::calculateOverallRating($fullReview);

$types = isset($fullReview['types']) ? json_decode($fullReview['types'], true) : [];
$genre = $fullReview['genre_name'] ?? '';

if (empty($genre)) {
    if (!empty($types) && is_array($types)) {
        $filtered = array_diff($types, ['restaurant', 'food', 'point_of_interest', 'establishment']);
        if (!empty($filtered)) {
            $genreStr = reset($filtered);
            $genreStr = str_replace('_', ' ', $genreStr);
            $genre = ucwords($genreStr);
        }
    }
}

function renderEditorJsHtml($jsonStr) {
    if (empty($jsonStr)) return '';
    $data = json_decode($jsonStr, true);
    
    // Fallback for old plain-text reviews before editor.js was added
    if (!$data || !isset($data['blocks'])) {
        return nl2br(htmlspecialchars($jsonStr));
    }
    
    $html = '';
    $allowedTags = '<b><i><a><u><br><strong><em><mark>';
    
    foreach ($data['blocks'] as $block) {
        $type = $block['type'] ?? '';
        $bData = $block['data'] ?? [];
        
        switch ($type) {
            case 'paragraph':
                $text = strip_tags($bData['text'] ?? '', $allowedTags);
                $html .= '<p style="margin-bottom: 1em;">' . $text . '</p>';
                break;
            case 'header':
                $level = isset($bData['level']) ? (int)$bData['level'] : 2;
                if ($level < 1 || $level > 6) $level = 2;
                $text = strip_tags($bData['text'] ?? '', $allowedTags);
                $html .= '<h' . $level . ' style="margin-top: 1.5em; margin-bottom: 0.5em; color: #333;">' . $text . '</h' . $level . '>';
                break;
            case 'list':
                $tag = (isset($bData['style']) && $bData['style'] === 'ordered') ? 'ol' : 'ul';
                $html .= '<' . $tag . ' style="margin-bottom: 1em; padding-left: 20px;">';
                if (isset($bData['items']) && is_array($bData['items'])) {
                    foreach ($bData['items'] as $item) {
                        $itemText = strip_tags((string)$item, $allowedTags);
                        $html .= '<li style="margin-bottom: 0.3em;">' . $itemText . '</li>';
                    }
                }
                $html .= '</' . $tag . '>';
                break;
            default:
                if (isset($bData['text'])) {
                    $text = strip_tags($bData['text'], $allowedTags);
                    $html .= '<p style="margin-bottom: 1em;">' . $text . '</p>';
                }
                break;
        }
    }
    return $html;
}

?>

<div style="padding: 20px; max-width: 900px; margin: 0 auto;">
    <a href="/wrv/food/pg_index.php" style="color: #6b4a8e; text-decoration: none; font-weight: bold; display: inline-block; margin-bottom: 25px;">&larr; Back to all Reviews</a>

    <div style="background-color: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 35px; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px;">
            <div>
                <h1 style="margin: 0 0 10px 0; color: #333; font-size: 2.2rem;"><?= htmlspecialchars($fullReview['title']) ?></h1>
                
                <div style="color: #666; font-size: 1.1rem; margin-bottom: 10px;">
                    <strong style="color: #6b4a8e; font-size: 1.2rem;"><?= htmlspecialchars($fullReview['place_name']) ?></strong>
                    <?php if ($genre): ?>
                        &nbsp;&bull;&nbsp; <span style="background: #f0f0f0; padding: 2px 8px; border-radius: 12px; font-size: 0.9em;"><?= htmlspecialchars($genre) ?></span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($fullReview['formatted_address'])): ?>
                    <div style="color: #777; font-size: 0.95rem; margin-bottom: 5px;">📍 <?= htmlspecialchars($fullReview['formatted_address']) ?></div>
                <?php endif; ?>

                <?php if (!empty($fullReview['date_visited'])): ?>
                    <div style="color: #999; font-size: 0.9rem; margin-top: 5px;">Visited on <?= date('F j, Y', strtotime($fullReview['date_visited'])) ?></div>
                <?php endif; ?>
            </div>            
            <?php if ($overallRating !== null): ?>
                <div style="background-color: #f8f9fa; border: 3px solid <?= $overallRating >= 7.5 ? '#4caf50' : ($overallRating >= 5 ? '#ff9800' : '#f44336') ?>; border-radius: 50%; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; flex-direction: column; flex-shrink: 0; margin-left: 20px;">
                    <span style="font-weight: bold; font-size: 1.8rem; color: #333; line-height: 1;"><?= number_format($overallRating, 1) ?></span>
                    <span style="font-size: 0.7rem; color: #666; font-weight: bold; text-transform: uppercase; margin-top: 4px;">Overall</span>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="color: #222; font-size: 1.1rem; line-height: 1.6; padding-top: 25px; border-top: 1px solid #eee; margin-bottom: 30px; letter-spacing: 0.3px;">
            <?= renderEditorJsHtml($fullReview['review_text']) ?>
        </div>
        
        <?php if (isset($fullReview['rating_product']) || isset($fullReview['rating_value'])): ?>
        <h3 style="margin-bottom: 15px; color: #555; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1px;">Rating Breakdown</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap; background: #fcfcfc; padding: 20px; border-radius: 8px; border: 1px solid #eaeaea;">
            <?php if (isset($fullReview['rating_product'])): ?>
                <div style="text-align: center; flex: 1; min-width: 90px;">
                    <div style="font-size: 0.85rem; color: #777; text-transform: uppercase; margin-bottom: 5px;">Product</div>
                    <div style="font-weight: bold; font-size: 1.4rem; color: #333;"><?= number_format($fullReview['rating_product'], 1) ?></div>
                </div>
            <?php endif; ?>
            <?php if (isset($fullReview['rating_service'])): ?>
                <div style="text-align: center; flex: 1; min-width: 90px; border-left: 1px solid #eaeaea;">
                    <div style="font-size: 0.85rem; color: #777; text-transform: uppercase; margin-bottom: 5px;">Service</div>
                    <div style="font-weight: bold; font-size: 1.4rem; color: #333;"><?= number_format($fullReview['rating_service'], 1) ?></div>
                </div>
            <?php endif; ?>
            <?php if (isset($fullReview['rating_atmosphere'])): ?>
                <div style="text-align: center; flex: 1; min-width: 90px; border-left: 1px solid #eaeaea;">
                    <div style="font-size: 0.85rem; color: #777; text-transform: uppercase; margin-bottom: 5px;">Atmosphere</div>
                    <div style="font-weight: bold; font-size: 1.4rem; color: #333;"><?= number_format($fullReview['rating_atmosphere'], 1) ?></div>
                </div>
            <?php endif; ?>
            <?php if (isset($fullReview['rating_value'])): ?>
                <div style="text-align: center; flex: 1; min-width: 90px; border-left: 1px solid #eaeaea;">
                    <div style="font-size: 0.85rem; color: #777; text-transform: uppercase; margin-bottom: 5px;">Value</div>
                    <div style="font-weight: bold; font-size: 1.4rem; color: #333;"><?= number_format($fullReview['rating_value'], 1) ?></div>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div style="margin-top: 30px; text-align: center;">
            <a href="/wrv/food/pg_create_review.php?place_pk=<?= (int)$fullReview['fk_places_place'] ?>" style="color: #3498db; text-decoration: none; font-weight: bold; font-size: 0.95rem;">&#x270E; Edit or Write Another Review for this Location</a>
        </div>
    </div>
</div>