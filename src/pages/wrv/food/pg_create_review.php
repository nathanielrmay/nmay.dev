<?php
namespace pages\wrv\food;

require_once __DIR__ . '/aFoodPage.php';

use lib\basket;

class pg_create_review extends aFoodPage {
    public function getPageTitle() {
        return "Find Restaurant - Wilma's Reviews";
    }
}
?>

<div style="padding: 20px; max-width: 800px;">
    <h2>Find a Restaurant</h2>
    
    <div id="selected-place" style="display: none; background-color: #e6ffe6; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #b3ffb3;">
        <strong>Selected:</strong> <span id="selected-place-name"></span>
        <input type="hidden" id="selected-place-pk" value="">
    </div>

    <?php 
    $searchArgs = [
        'error' => null
    ];
    echo basket::render('pages/wrv/food/lib/partials/pt_search_places.php', $searchArgs); 
    ?>

    <script>
    // Listen for place selection from the partial
    document.addEventListener('placeSelected', function(e) {
        var data = e.detail;
        document.getElementById('selected-place').style.display = 'block';
        document.getElementById('selected-place-name').textContent = data.name + ' (pk: ' + data.pk + ')';
        document.getElementById('selected-place-pk').value = data.pk;
    });
    </script>
</div>
