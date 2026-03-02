<?php
namespace pages\wrv\food;

require_once __DIR__ . '/aFoodPage.php';

use lib\basket;
use lib\db\models\wrv\db_food_review;
use lib\db\models\wrv\db_food_review_rating;

class pg_create_review extends aFoodPage {
    public function getPageTitle() {
        return "Find Restaurant - Wilma's Reviews";
    }
}

$db = basket::db_web();
$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $placePk = $_POST['place_pk'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $text = trim($_POST['review_text'] ?? '');
    $dateVisited = $_POST['date_visited'] ?? null;
    
    // Ratings
    $rProduct = $_POST['rating_product'] ?? null;
    $rValue = $_POST['rating_value'] ?? null;
    $rService = $_POST['rating_service'] ?? null;
    $rAtmosphere = $_POST['rating_atmosphere'] ?? null;
    $rOverall = $_POST['rating_overall'] ?? null;

    if (!$placePk || empty($title) || empty($text)) {
        $error = "Restaurant, Title, and Review Text are required.";
    } else {
        $reviewModel = new db_food_review($db);
        $newReviewPk = $reviewModel->write((int)$placePk, $title, $text, $dateVisited);

        if ($newReviewPk) {
            $ratingModel = new db_food_review_rating($db);
            $ratingModel->write($newReviewPk, [
                'rating_product' => $rProduct,
                'rating_value' => $rValue,
                'rating_service' => $rService,
                'rating_atmosphere' => $rAtmosphere,
                'rating_overall' => $rOverall
            ]);
            $success = true;
        } else {
            $error = "There was an error saving your review.";
        }
    }
}
?>

<div style="padding: 20px; max-width: 800px;">
    <h2>Find a Restaurant</h2>
    
    <div id="selected-place" style="display: none; background-color: #e6ffe6; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #b3ffb3;">
        <strong>Selected:</strong> <span id="selected-place-name"></span>
        <input type="hidden" id="selected-place-pk" value="">
    </div>

    <?php if ($success): ?>
        <div style="background-color: #e6ffe6; color: #008000; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #b3ffb3; font-weight: bold;">
            Review submitted successfully!
            <br><br>
            <a href="/wrv/food/pg_create_review.php" style="color: #008000; text-decoration: underline;">Write another review</a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div style="background-color: #fee; color: #c00; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php 
        $searchArgs = [
            'error' => null
        ];
        echo basket::render('pages/wrv/food/lib/partials/pt_search_places.php', $searchArgs); 
        ?>

        <!-- Review Form (Hidden initially until place is selected) -->
        <form method="POST" action="/wrv/food/pg_create_review.php" id="review-form" style="display: none; background-color: #fafafa; padding: 25px; border-radius: 8px; border: 1px solid #ddd; margin-top: 30px;">
            <input type="hidden" name="place_pk" id="form-place-pk" value="">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Review Title <span style="color: red;">*</span></label>
                <input type="text" name="title" required placeholder="e.g. Best tacos in town!" style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Date Visited</label>
                <input type="date" name="date_visited" style="padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Your Review <span style="color: red;">*</span></label>
                <textarea name="review_text" required rows="8" placeholder="Write your full review here..." style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; font-family: inherit; resize: vertical;"></textarea>
            </div>

            <h3 style="margin-top: 30px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Structured Ratings (0 - 10)</h3>
            <p style="color: #666; font-size: 0.9em; margin-bottom: 20px;">Rate the following categories from 0 to 10. Decimals are allowed (e.g. 8.5).</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Food / Product</label>
                    <input type="number" name="rating_product" min="0" max="10" step="0.1" style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Service</label>
                    <input type="number" name="rating_service" min="0" max="10" step="0.1" style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Atmosphere</label>
                    <input type="number" name="rating_atmosphere" min="0" max="10" step="0.1" style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Value</label>
                    <input type="number" name="rating_value" min="0" max="10" step="0.1" style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Overall</label>
                    <input type="number" name="rating_overall" min="0" max="10" step="0.1" style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; background-color: #f0f8ff;">
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" name="submit_review" value="1" style="padding: 12px 30px; background-color: #38827e; color: white; border: none; border-radius: 5px; font-size: 1.1rem; font-weight: bold; cursor: pointer;">Post Review</button>
            </div>
        </form>
    <?php endif; ?>

    <script>
    // Listen for place selection from the partial
    document.addEventListener('placeSelected', function(e) {
        var data = e.detail;
        document.getElementById('selected-place').style.display = 'block';
        document.getElementById('selected-place-name').textContent = data.name + ' (pk: ' + data.pk + ')';
        document.getElementById('form-place-pk').value = data.pk;
        document.getElementById('review-form').style.display = 'block';
    });
    </script>
</div>
