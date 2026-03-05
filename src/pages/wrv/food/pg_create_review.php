<?php
namespace pages\wrv\food;

require_once __DIR__ . '/aFoodPage.php';

use lib\basket;
use lib\db\models\wrv\db_food_review;
use lib\db\models\wrv\db_food_review_rating;
use lib\db\models\wrv\db_food_review_type;

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
    
    // Ratings - Integer Only
    $rProduct = isset($_POST['rating_product']) && $_POST['rating_product'] !== '' ? (int)$_POST['rating_product'] : null;
    $rValue = isset($_POST['rating_value']) && $_POST['rating_value'] !== '' ? (int)$_POST['rating_value'] : null;
    $rService = isset($_POST['rating_service']) && $_POST['rating_service'] !== '' ? (int)$_POST['rating_service'] : null;
    $rAtmosphere = isset($_POST['rating_atmosphere']) && $_POST['rating_atmosphere'] !== '' ? (int)$_POST['rating_atmosphere'] : null;
    $reviewTypePk = $_POST['review_type'] ?? null;

    if (!$placePk || empty($title) || empty($text)) {
        $error = "Restaurant, Title, and Review Text are required.";
    } else {
        $reviewModel = new db_food_review($db);
        $newReviewPk = $reviewModel->write((int)$placePk, $title, $text, $dateVisited, null, $reviewTypePk === '' ? null : (int)$reviewTypePk);

        if ($newReviewPk) {
            $ratingError = false;
            // Only write ratings if at least one category was filled out
            if ($rProduct !== null || $rValue !== null || $rService !== null || $rAtmosphere !== null) {
                $ratingModel = new db_food_review_rating($db);
                $res = $ratingModel->write($newReviewPk, [
                    'rating_product' => $rProduct,
                    'rating_value' => $rValue,
                    'rating_service' => $rService,
                    'rating_atmosphere' => $rAtmosphere
                ]);

                if (is_string($res)) {
                    $error = "Review saved, but ratings failed: " . $res;
                    $ratingError = true;
                } else if ($res === false) {
                    $error = "Review saved, but ratings returned fatal error.";
                    $ratingError = true;
                }
            }
            if (!$ratingError) {
                $success = true;
            }
        } else {
            $error = "There was an error saving your review.";
        }
    }
}

$typeModel = new db_food_review_type($db);
$dbGenres = $typeModel->readAll();
?>

<div style="padding: 20px; max-width: 800px;">
    <h2>Find a Restaurant</h2>
    
    <div id="place-partial-container" style="display: none;"></div>

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

        <div id="search-partial-container">
            <?php 
            $searchArgs = [
                'error' => null
            ];
            echo basket::render('pages/wrv/food/lib/partials/pt_search_places.php', $searchArgs); 
            ?>
        </div>
        <!-- Review Form (Hidden initially until place is selected) -->
        <form method="POST" action="/wrv/food/pg_create_review.php" id="review-form" style="display: none; background-color: #fafafa; padding: 25px; border-radius: 8px; border: 1px solid #ddd; margin-top: 30px;">
            <input type="hidden" name="place_pk" id="form-place-pk" value="">
            <input type="hidden" name="submit_review" value="1">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Review Title <span style="color: red;">*</span></label>
                <input type="text" name="title" required placeholder="e.g. Best tacos in town!" style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Date Visited</label>
                <input type="date" name="date_visited" style="padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Genre <span style="color: red;">*</span></label>
                <select name="review_type" required style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc; background-color: #fff;">
                    <option value="">Select a Genre...</option>
                    <?php foreach ($dbGenres as $g): ?>
                        <option value="<?= htmlspecialchars($g['pk']) ?>"><?= htmlspecialchars($g['name']) ?></option>
                    <?php endforeach; ?>
                    <option value="">Other</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 8px;">Your Review <span style="color: red;">*</span></label>
                <input type="hidden" name="review_text" id="review_text_hidden" value="">
                <div id="editorjs" style="background: #fff; border: 1px solid #ccc; padding: 10px; border-radius: 4px; min-height: 200px; font-family: inherit;"></div>
            </div>

            <h3 style="margin-top: 30px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Structured Ratings (0 - 10)</h3>
            <p style="color: #666; font-size: 0.9em; margin-bottom: 20px;">Rate the following categories from 0 to 10 (Whole numbers only).</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Food / Product</label>
                    <input type="number" name="rating_product" min="0" max="10" step="1" style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Service</label>
                    <input type="number" name="rating_service" min="0" max="10" step="1" style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Atmosphere</label>
                    <input type="number" name="rating_atmosphere" min="0" max="10" step="1" style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
                </div>
                <div>
                    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Value</label>
                    <input type="number" name="rating_value" min="0" max="10" step="1" style="width: 100%; padding: 10px; font-size: 1rem; border-radius: 4px; border: 1px solid #ccc;">
                </div>
            </div>

            <div style="text-align: right;">
                <button type="submit" style="padding: 12px 30px; background-color: #38827e; color: white; border: none; border-radius: 5px; font-size: 1.1rem; font-weight: bold; cursor: pointer;">Post Review</button>
            </div>
        </form>
    <?php endif; ?>

    <script src="/lib/js/ext/editorjs/editor.js"></script>
    <script src="/lib/js/ext/editorjs/header.js"></script>
    <script src="/lib/js/ext/editorjs/list.js"></script>
    <script>    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // Listen for place selection from the partial
    document.addEventListener('placeSelected', function(e) {
        var data = e.detail;
        
        // Populate and show the hidden form fields
        document.getElementById('form-place-pk').value = data.pk;
        document.getElementById('review-form').style.display = 'block';

        // Hide search, show loading state in place container
        document.getElementById('search-partial-container').style.display = 'none';
        var placeContainer = document.getElementById('place-partial-container');
        placeContainer.style.display = 'block';
        placeContainer.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">Loading place details...</div>';

        // Fetch the rendered partial layout
        fetch('/wrv/food/idc_war/lib/ajax/pg_api_render_place.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ pk: data.pk })
        })
        .then(function(res) { return res.json(); })
        .then(function(resData) {
            if (resData.error) {
                placeContainer.innerHTML = '<div style="color: #c00; padding: 15px;">Error loading place details: ' + escapeHtml(resData.error) + '</div>';
            } else if (resData.html) {
                placeContainer.innerHTML = resData.html;
            }
        })
        .catch(function(err) {
            placeContainer.innerHTML = '<div style="color: #c00; padding: 15px;">Network error loading details.</div>';
        });
    });

    // Clear function invoked by the pt_place template
    window.clearSelectedPlace = function() {
        // Reset form variables and hide review block
        document.getElementById('form-place-pk').value = '';
        document.getElementById('review-form').style.display = 'none';

        // Hide the place layout
        var placeContainer = document.getElementById('place-partial-container');
        placeContainer.style.display = 'none';
        placeContainer.innerHTML = '';

        // Show the search block again
        document.getElementById('search-partial-container').style.display = 'block';
    };

    // Editor.js Instantiation
    var editor;
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof EditorJS !== 'undefined') {
            editor = new EditorJS({
                holder: 'editorjs',
                placeholder: 'Write your full review here...',
                tools: {
                    header: {
                        class: Header,
                        inlineToolbar: ['link']
                    },
                    list: {
                        class: EditorjsList,
                        inlineToolbar: true
                    }
                }
            });
        }
    });

    // Intercept form submission to serialize editor data
    document.getElementById('review-form').addEventListener('submit', function(e) {
        if (editor) {
            e.preventDefault(); // Stop native submission
            var form = this;
            editor.save().then(function(outputData) {
                // Populate hidden input with stringified JSON
                document.getElementById('review_text_hidden').value = JSON.stringify(outputData);
                form.submit(); // Programmatically submit
            }).catch(function(error) {
                console.error('Editor.js saving failed: ', error);
                alert("Failed to save review text.");
            });
        }
    });
    </script>
</div>