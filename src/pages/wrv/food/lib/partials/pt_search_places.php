<div class="pt-search-places">
    <?php if (!empty($error)): ?>
        <div style="background-color: #fee; color: #c00; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php $canAddRestaurants = $canAddRestaurants ?? true; ?>

    <!-- Roster managed by JS (above search so it stays visible) -->
    <?php $hasRoster = !empty($showRoster); ?>
    <?php if ($hasRoster): ?>
    <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #ddd;">
        <h3>Current Roster (<span id="roster-count">0</span>)</h3>
        <div id="roster-list">
            <p style="color: #999; font-style: italic;">No restaurants added yet.</p>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($canAddRestaurants): ?>
        <!-- Search Form (AJAX, no page reload) -->
        <form id="places-search-form" style="margin-bottom: 30px; display: flex; gap: 10px;">
            <input type="text" id="search-query" placeholder="Enter restaurant name (e.g. Taco Bell)" required 
                   style="flex: 2; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 4px;"
                   value="<?= htmlspecialchars($searchQuery ?? '') ?>">
            <input type="text" id="search-location" placeholder="Location" required 
                   style="flex: 1; padding: 10px; font-size: 1rem; border: 1px solid #ccc; border-radius: 4px;"
                   value="<?= htmlspecialchars($searchLocation ?? 'Springfield, Missouri') ?>">
            <button type="submit" id="search-btn" style="padding: 10px 20px; background-color: #6b4a8e; color: white; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer;">
                Search Maps
            </button>
        </form>

        <!-- Search Results (populated by JS) -->
        <div id="search-results"></div>
    <?php else: ?>
        <p style="color: #666; font-style: italic; margin-bottom: 20px;">Restaurants can only be added when the war is in "creation" status.</p>
    <?php endif; ?>

    <script>
    (function() {
        var canAdd = <?= json_encode($canAddRestaurants) ?>;
        var hasRoster = <?= json_encode($hasRoster) ?>;
        var roster = <?= $initialRosterJson ?? '[]' ?>;

        // --- Search ---
        var searchForm = document.getElementById('places-search-form');
        if (searchForm) {
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var query = document.getElementById('search-query').value.trim();
                var location = document.getElementById('search-location').value.trim();
                var btn = document.getElementById('search-btn');
                var resultsDiv = document.getElementById('search-results');

                if (!query) return;

                btn.disabled = true;
                btn.textContent = 'Searching...';
                resultsDiv.innerHTML = '<p style="color: #666;">Searching...</p>';

                fetch('/wrv/food/idc_war/lib/ajax/pg_api_search_places.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ query: query, location: location })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    btn.disabled = false;
                    btn.textContent = 'Search Maps';

                    if (data.error) {
                        resultsDiv.innerHTML = '<p style="color: #c00;">Error: ' + escapeHtml(data.error) + '</p>';
                        return;
                    }

                    if (!data.results || data.results.length === 0) {
                        resultsDiv.innerHTML = '<p style="color: #666;">No results found for that search.</p>';
                        return;
                    }

                    var html = '<h3>Results</h3><div style="display: flex; flex-direction: column; gap: 15px;">';
                    data.results.forEach(function(r) {
                        html += '<div style="border: 1px solid #eee; padding: 15px; border-radius: 5px; display: flex; justify-content: space-between; align-items: center; background-color: #fafafa;">';
                        html += '<div>';
                        html += '<strong style="font-size: 1.1em; color: #333;">' + escapeHtml(r.name) + '</strong>';
                        html += '<div style="color: #666; font-size: 0.9em; margin-top: 5px;">' + escapeHtml(r.formatted_address) + '</div>';
                        html += '</div>';
                        html += '<button type="button" onclick="selectPlace(\'' + escapeAttr(r.place_id) + '\', \'' + escapeAttr(r.name) + '\')" ';
                        html += 'style="padding: 8px 15px; background-color: #38827e; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; white-space: nowrap;">';
                        html += '+ Add</button>';
                        html += '</div>';
                    });
                    html += '</div>';
                    resultsDiv.innerHTML = html;
                })
                .catch(function(err) {
                    btn.disabled = false;
                    btn.textContent = 'Search Maps';
                    resultsDiv.innerHTML = '<p style="color: #c00;">Network error: ' + escapeHtml(err.message) + '</p>';
                });
            });
        }

        // --- Place selection (resolve via AJAX, then add to roster or callback) ---
        window.selectPlace = function(googlePlaceId, placeName) {
            fetch('/wrv/food/idc_war/lib/ajax/pg_api_places.php', {
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

                if (hasRoster) {
                    // Check for duplicate
                    for (var i = 0; i < roster.length; i++) {
                        if (roster[i].google_place_id === data.id) {
                            alert(data.name + ' is already in the roster!');
                            return;
                        }
                    }
                    roster.push({
                        place_pk: data.pk,
                        place_name: data.name,
                        google_place_id: data.id
                    });
                    renderRoster();
                }

                // Fire a custom event so parent pages can react
                document.dispatchEvent(new CustomEvent('placeSelected', { detail: data }));
            })
            .catch(function(err) {
                alert('Network error: ' + err.message);
            });
        };

        // --- Roster rendering ---
        function renderRoster() {
            if (!hasRoster) return;
            var list = document.getElementById('roster-list');
            var count = document.getElementById('roster-count');
            var jsonInput = document.getElementById('restaurants-json');

            count.textContent = roster.length;
            if (jsonInput) jsonInput.value = JSON.stringify(roster);
            // Also sync to submit form if it exists
            var submitJson = document.getElementById('submit-restaurants-json');
            if (submitJson) submitJson.value = JSON.stringify(roster);

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
                if (canAdd) {
                    html += '<button type="button" onclick="rosterRemove(' + i + ')" style="padding: 5px 12px; background-color: #e74c3c; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85em;">✕</button>';
                }
                html += '</div>';
            });
            html += '</div>';
            list.innerHTML = html;
        }

        window.rosterRemove = function(index) {
            roster.splice(index, 1);
            renderRoster();
        };

        // --- Helpers ---
        function escapeHtml(text) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }

        function escapeAttr(text) {
            return text.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
        }

        // Initial render
        renderRoster();
    })();
    </script>
</div>
