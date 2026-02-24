<?php
/**
 * @var string|null $date Date to filter by (YYYY-MM-DD), or null for latest.
 * @var int $columns Number of columns for the layout (default 1).
 * @var array|null $newsData Optional: Pass pre-fetched data to skip internal fetch.
 * @var float $fontSizeScale Scaling factor for fonts (default 1.0).
 */

use lib\basket;
use lib\db\models\panal\nba\db_news;

// Defaults
$date = $date ?? null;
$columns = $columns ?? 1;
$isOpen = $isOpen ?? true;
$limit = 20;
$fontSizeScale = $fontSizeScale ?? 1.0;

// Fetch data if not provided
if (!isset($newsData)) {
    $db = basket::db_panal();
    $newsModel = new db_news($db);
    $newsData = $newsModel->read($date, $limit);
}
?>

<div class="news-container" style="--news-scale: <?= $fontSizeScale ?>;">
    <details <?= $isOpen ? 'open' : '' ?>>
        <summary>NEWS</summary>

        <?php if (empty($newsData)): ?>
            <p class="no-news">No news available.</p>
        <?php else: ?>
            <div class="news-grid" style="grid-template-columns: repeat(<?= $columns ?>, 1fr);">
                <?php foreach ($newsData as $item): 
                    $title = $item['title'] ?? 'Untitled';
                    // Try common RSS link fields
                    $link = $item['link'] ?? $item['url'] ?? $item['guid'] ?? '#';
                    
                    $desc = $item['summary'] ?? $item['description'] ?? ''; // Try summary first
                    // Strip HTML tags from description for clean newspaper look
                    $desc = strip_tags($desc);
                    if (strlen($desc) > 200) $desc = substr($desc, 0, 200) . '...';
                    
                    $source = $item['source'] ?? 'Unknown Source';
                    $pubDate = $item['published_at'] ?? '';
                    if ($pubDate) $pubDate = date('M j, g:i A', strtotime($pubDate));
                ?>
                    <div class="news-item">
                        <!-- Line 1: Date | Source | Title -->
                        <div class="news-header-line">
                            <span class="news-time"><?= htmlspecialchars($pubDate) ?></span>
                            <span class="sep">|</span>
                            <span class="news-source"><?= htmlspecialchars($source) ?></span>
                            <span class="sep">|</span>
                            <a href="<?= htmlspecialchars($link) ?>" target="_blank" class="news-title">
                                <?= htmlspecialchars($title) ?>
                            </a>
                        </div>
                        
                        <div class="news-rule"></div>

                        <!-- Line 2: Summary -->
                        <p class="news-desc"><?= htmlspecialchars($desc) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </details>
</div>

<link rel="stylesheet" href="/pages/sports_anals/nba/lib/partials/news/pt_news.css">