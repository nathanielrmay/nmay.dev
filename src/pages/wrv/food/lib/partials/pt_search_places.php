<div class="pt-search-places">
    <?php if (!empty($error)): ?>
        <div style="background-color: #fee; color: #c00; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Search Form -->
    <form method="POST" action="<?= htmlspecialchars($formAction) ?>" style="margin-bottom: 30px; display: flex; gap: 10px;">
        <input type="text" name="search_query" placeholder="Enter restaurant name (e.g. Taco Bell)" required 
               style="flex: 2; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 4px;"
               value="<?= htmlspecialchars($searchQuery ?? '') ?>">
        <input type="text" name="location" placeholder="Location" required 
               style="flex: 1; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 4px;"
               value="<?= htmlspecialchars($searchLocation ?? 'Springfield, Missouri') ?>">
        <button type="submit" style="padding: 10px 20px; background-color: #6b4a8e; color: white; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer;">
            Search Maps
        </button>
    </form>

    <!-- Search Results -->
    <?php if (!empty($searchResults)): ?>
        <h3>Results</h3>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach ($searchResults as $result): ?>
                <div style="border: 1px solid #eee; padding: 15px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; background-color: #fafafa;">
                    <div>
                        <strong style="font-size: 1.1em; color: #333;"><?= htmlspecialchars($result['name']) ?></strong>
                        <div style="color: #666; font-size: 0.9em; margin-top: 5px;">
                            <?= htmlspecialchars($result['formatted_address']) ?>
                        </div>
                    </div>
                    <form method="POST" action="<?= htmlspecialchars($formAction) ?>" style="margin: 0;">
                        <input type="hidden" name="place_id" value="<?= htmlspecialchars($result['place_id']) ?>">
                        <input type="hidden" name="place_name" value="<?= htmlspecialchars($result['name']) ?>">
                        <button type="submit" style="padding: 8px 15px; background-color: #38827e; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                            Select
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_query'])): ?>
        <p style="color: #666;">No results found for that search.</p>
    <?php endif; ?>
</div>
