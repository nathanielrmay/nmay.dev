<div class="pt-search-places">
    <?php if (!empty($error)): ?>
        <div style="background-color: #fee; color: #c00; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php $canAddRestaurants = $canAddRestaurants ?? true; ?>

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

        // --- Place selection (resolve via AJAX, then callback) ---
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

                // Fire a custom event so parent pages can react
                document.dispatchEvent(new CustomEvent('placeSelected', { detail: data }));
            })
            .catch(function(err) {
                alert('Network error: ' + err.message);
            });
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
    })();
    </script>
</div>
