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
                    <?php if (!empty($useJsSelect)): ?>
                        <button type="button" 
                                onclick="rosterAddPlace('<?= htmlspecialchars($result['place_id'], ENT_QUOTES) ?>', '<?= htmlspecialchars(addslashes($result['name']), ENT_QUOTES) ?>')"
                                style="padding: 8px 15px; background-color: #38827e; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                            + Add
                        </button>
                    <?php else: ?>
                        <form method="POST" action="<?= htmlspecialchars($formAction) ?>" style="margin: 0;">
                            <input type="hidden" name="place_id" value="<?= htmlspecialchars($result['place_id']) ?>">
                            <input type="hidden" name="place_name" value="<?= htmlspecialchars($result['name']) ?>">
                            <button type="submit" style="padding: 8px 15px; background-color: #38827e; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                                Select
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_query'])): ?>
        <p style="color: #666;">No results found for that search.</p>
    <?php endif; ?>

    <?php if (!empty($useJsSelect)): ?>
    <!-- Roster managed by JS -->
    <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #ddd;">
        <h3>Current Roster (<span id="roster-count">0</span>)</h3>
        <div id="roster-list">
            <p style="color: #999; font-style: italic;">No restaurants added yet.</p>
        </div>
    </div>
    <input type="hidden" name="restaurants_json" id="restaurants-json" value='<?= htmlspecialchars($initialRosterJson ?? '[]') ?>'>

    <script>
    (function() {
        var roster = <?= $initialRosterJson ?? '[]' ?>;

        function renderRoster() {
            var list = document.getElementById('roster-list');
            var count = document.getElementById('roster-count');
            var jsonInput = document.getElementById('restaurants-json');

            count.textContent = roster.length;
            jsonInput.value = JSON.stringify(roster);

            if (roster.length === 0) {
                list.innerHTML = '<p style="color: #999; font-style: italic;">No restaurants added yet.</p>';
                return;
            }

            var html = '<div style="display: flex; flex-direction: column; gap: 10px;">';
            roster.forEach(function(r, i) {
                html += '<div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; background-color: #fff; border: 1px solid #ddd; border-radius: 5px;">';
                html += '<div>';
                html += '<span style="font-weight: bold; color: #6b4a8e; margin-right: 8px;">' + (i + 1) + '.</span>';
                html += '<span style="font-size: 1.05em; color: #333;">' + escapeHtml(r.place_name) + '</span>';
                html += '</div>';
                html += '<button type="button" onclick="rosterRemove(' + i + ')" style="padding: 5px 12px; background-color: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85em;">✕</button>';
                html += '</div>';
            });
            html += '</div>';
            list.innerHTML = html;
        }

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        window.rosterRemove = function(index) {
            roster.splice(index, 1);
            renderRoster();
        };

        window.rosterAddPlace = function(googlePlaceId, placeName) {
            // Check for duplicate
            for (var i = 0; i < roster.length; i++) {
                if (roster[i].google_place_id === googlePlaceId) {
                    alert(placeName + ' is already in the roster!');
                    return;
                }
            }

            // AJAX call to resolve/create the place
            fetch('/wrv/food/pg_api_places.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ place_id: googlePlaceId, place_name: placeName })
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }
                roster.push({
                    place_pk: data.pk,
                    place_name: data.name,
                    google_place_id: data.id
                });
                renderRoster();
            })
            .catch(function(err) {
                alert('Network error: ' + err.message);
            });
        };

        // Initial render
        renderRoster();
    })();
    </script>
    <?php endif; ?>
</div>
