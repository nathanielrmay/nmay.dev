<?php

namespace lib\partials;

use lib\contracts\aPartial;

/**
 * Class tabbed_container
 * 
 * A reusable partial that creates a tabbed interface.
 * 
 * @package lib\partials
 */
class tabbed_container extends aPartial
{
    /** @var string A unique ID for this instance to avoid collisions on the same page. */
    private string $id;

    public function __construct(array $contentPages = [])
    {
        parent::__construct($contentPages);
        $this->id = 'tab-' . bin2hex(random_bytes(4));
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}

/**
 * Implementation logic for rendering.
 * When basket::render is called, it extracts variables from the $args array.
 * We expect 'tabs' or 'contentPages' to be passed.
 */

$contentPages = $contentPages ?? $tabs ?? [];
$container = new tabbed_container($contentPages);
$pages = $container->getContentPages();
$containerId = $container->getId();
?>

<div class="tab-container" id="<?= $containerId ?>">
    <div class="tabs">
        <?php $first = true; foreach ($pages as $label => $content): ?>
            <button class="tab <?= $first ? 'active' : '' ?>" data-tab="<?= $containerId ?>-<?= md5($label) ?>">
                <?= htmlspecialchars($label) ?>
            </button>
            <?php $first = false; endforeach; ?>
    </div>
    <div class="tab-content-wrapper">
        <?php $first = true; foreach ($pages as $label => $content): ?>
            <div class="tab-content <?= $first ? 'active' : '' ?>" id="<?= $containerId ?>-<?= md5($label) ?>">
                <?= $content ?>
            </div>
            <?php $first = false; endforeach; ?>
    </div>
</div>

<style>
    .tab-container {
        width: 100%;
        margin: 1rem 0;
    }
    .tabs {
        display: flex;
        gap: 5px;
        border-bottom: 2px solid #2b2b2b;
        margin-bottom: 15px;
    }
    .tab {
        padding: 10px 20px;
        cursor: pointer;
        border: 2px solid #2b2b2b;
        border-bottom: none;
        background: #eee;
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        text-transform: uppercase;
        border-radius: 8px 8px 0 0;
        transition: all 0.3s ease;
        font-size: 0.9em;
    }
    .tab:hover {
        background: #ddd;
    }
    .tab.active {
        background: #333;
        color: #fff;
        border-color: #333;
    }
    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease;
    }
    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<script>
    (function() {
        const container = document.getElementById('<?= $containerId ?>');
        if (!container) return;
        
        const tabs = container.querySelectorAll('.tab');
        const contents = container.querySelectorAll('.tab-content');

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                const targetId = tab.getAttribute('data-tab');

                // Remove active class from all tabs and contents in THIS container
                tabs.forEach((t) => t.classList.remove('active'));
                contents.forEach((c) => c.classList.remove('active'));

                // Add active class to clicked tab and target content
                tab.classList.add('active');
                const targetContent = document.getElementById(targetId);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
            });
        });
    })();
</script>
