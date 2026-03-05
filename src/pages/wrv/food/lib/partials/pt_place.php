<?php
/**
 * Partial to display full details of a saved place.
 * Expects $args['place'] and $args['cache'] (from db_places_place & db_places_cache)
 */
namespace pages\wrv\food\lib\partials;

$p = $args['place'] ?? null;
$c = $args['cache'] ?? null;

if (!$p) return;

$name = $p['name'] ?? 'Unknown Place';
$address = $c['formatted_address'] ?? '';
$phone = $c['phone_number'] ?? '';
$rating = $c['rating'] ?? '';
$userRatingsTotal = $c['user_ratings_total'] ?? '';
$priceLevel = $c['price_level'] ?? null;
$openNow = $c['open_now'] ?? null;
$website = $c['website'] ?? '';
$url = $c['url'] ?? '';

// Build price string (e.g. $$)
$priceStr = '';
if ($priceLevel !== null) {
    $priceStr = str_repeat('$', (int)$priceLevel);
}
?>

<div class="pt-place-card" style="border: 1px solid #ddd; padding: 25px; border-radius: 8px; background-color: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.05); margin-bottom: 25px;">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
        <div>
            <h3 style="margin: 0 0 5px 0; color: #333; font-size: 1.5rem;"><?= htmlspecialchars($name) ?></h3>
            <?php if ($address): ?>
                <div style="color: #666; font-size: 1rem; margin-bottom: 5px;"><?= htmlspecialchars($address) ?></div>
            <?php endif; ?>
        </div>
        
        <button type="button" onclick="clearSelectedPlace()" style="padding: 8px 15px; background-color: #f1f1f1; color: #555; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; font-size: 0.9rem; font-weight: bold; transition: background-color 0.2s;">
            &#10005; Clear / Search Again
        </button>
    </div>

    <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: center; margin-bottom: 15px; font-size: 0.95rem;">
        <?php if ($rating): ?>
            <div style="display: flex; align-items: center; gap: 5px;">
                <span style="color: #f39c12; font-size: 1.2rem;">&#9733;</span>
                <strong style="color: #333;"><?= htmlspecialchars((string)$rating) ?></strong>
                <?php if ($userRatingsTotal): ?>
                    <span style="color: #999;">(<?= htmlspecialchars((string)$userRatingsTotal) ?> reviews)</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($priceStr): ?>
            <div style="color: #27ae60; font-weight: bold; background-color: #e8f8f0; padding: 2px 8px; border-radius: 4px;">
                <?= htmlspecialchars($priceStr) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($openNow !== null): ?>
            <div style="display: flex; align-items: center;">
                <?php $isOpen = ($openNow === true || $openNow === 1 || $openNow === '1' || $openNow === 'true'); ?>
                <?php if ($isOpen): ?>
                    <span style="color: #2ecc71; font-weight: bold;">&#10004; Open Now</span>
                <?php else: ?>
                    <span style="color: #e74c3c; font-weight: bold;">&#10006; Closed</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.95rem; border-top: 1px solid #eee; padding-top: 15px;">
        <?php if ($phone): ?>
            <div><span style="color: #4a6b8e; margin-right: 8px;">&#128222;</span> <a href="tel:<?= htmlspecialchars($phone) ?>" style="color: #333; text-decoration: none;"><?= htmlspecialchars($phone) ?></a></div>
        <?php endif; ?>
        
        <?php if ($website): ?>
            <div><span style="color: #8e44ad; margin-right: 8px;">&#127760;</span> <a href="<?= htmlspecialchars($website) ?>" target="_blank" rel="noopener noreferrer" style="color: #3498db; text-decoration: none;">Website</a></div>
        <?php endif; ?>
        
        <?php if ($url): ?>
            <div><span style="color: #e67e22; margin-right: 8px;">&#128506;</span> <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener noreferrer" style="color: #3498db; text-decoration: none;">View on Google Maps</a></div>
        <?php endif; ?>
    </div>
</div>
