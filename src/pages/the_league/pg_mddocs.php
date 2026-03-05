<?php
namespace pages\the_league;

require_once __DIR__ . '/aLeaguePage.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/php/ext/Parsedown.php';

class pg_mddocs extends aLeaguePage
{
    public function getPageTitle()
    {
        $doc = $this->getDocName();
        if ($doc) {
            return ucwords(str_replace(['-', '_'], ' ', $doc)) . " - The League";
        }
        return "Docs - The League";
    }

    private function getDocName(): ?string
    {
        $doc = $_GET['doc'] ?? null;
        if ($doc && preg_match('/^[a-zA-Z0-9_-]+$/', $doc)) {
            return $doc;
        }
        return null;
    }
}
?>

<?php
$doc = $_GET['doc'] ?? null;

if ($doc && preg_match('/^[a-zA-Z0-9_-]+$/', $doc)) {
    $docPath = __DIR__ . '/docs/' . $doc . '.md';

    if (file_exists($docPath)) {
        $markdown = file_get_contents($docPath);
        $parsedown = new \Parsedown();
        $parsedown->setSafeMode(true);
        echo '<div class="md-content">';
        echo $parsedown->text($markdown);
        echo '</div>';
    }
    else {
        echo '<div class="md-content md-not-found">';
        echo '<h2>Document Not Found</h2>';
        echo '<p>The document <strong>' . htmlspecialchars($doc) . '</strong> could not be found.</p>';
        echo '<p><a href="/the_league/pg_index.php">Back to The League</a></p>';
        echo '</div>';
    }
}
else {
    echo '<div class="md-content">';
    echo '<h2>League Documents</h2>';
    echo '<p>Select a document from the sidebar to view it.</p>';
    echo '</div>';
}
?>
