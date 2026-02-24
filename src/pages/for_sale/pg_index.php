<?php
namespace pages\for_sale;

require_once __DIR__ . '/aForSalePage.php';

class pg_index extends aForSalePage {
    public function getPageTitle() {
        return "NMAY Classifieds - Collectibles";
    }
}
?>

<div class="classifieds-container">
    <div class="classifieds-header">
        <h1>Classified <span class="highlight">Ads</span></h1>
        <p class="subtitle">Volume 42 | Sunday Edition | Price: $0.05</p>
    </div>

    <div class="ads-grid">
        <!-- The Main Silly Item -->
        <div class="ad-item featured-ad">
            <div class="ad-header">
                <span class="ad-category">FOOD & BEVERAGE</span>
                <span class="ad-price">$4,500 OBO</span>
            </div>
            <h2 class="ad-title">VINTAGE 1994 MUSTARD-ONLY HOTDOG</h2>
            <p class="ad-description">
                Rare opportunity to own a piece of culinary history. This authentic 1994 ballpark frank has been 
                meticulously preserved in a vacuum-sealed humidified safe. Features a single, aggressive stripe 
                of bright yellow mustard. Absolutely NO KETCHUP has ever touched this item. 
            </p>
            <p class="ad-description">
                Minor superficial cracking consistent with age. Bun is fossilized to a perfect mahogany sheen. 
                Would make an excellent centerpiece for a data science lab or a very confusing gift for a loved one.
            </p>
            <div class="ad-footer">
                <span class="ad-condition">Condition: Antique / Non-edible</span>
                <a href="mailto:void@nmay.dev" class="contact-btn">INQUIRE</a>
            </div>
        </div>

        <!-- Smaller Filler Ads -->
        <div class="ad-item">
            <div class="ad-header">
                <span class="ad-category">SERVICES</span>
                <span class="ad-price">TRADE</span>
            </div>
            <h3 class="ad-title-small">PINEAPPLE PIZZA DEFENSE</h3>
            <p class="ad-description-small">
                Professional debate services for your next dinner party. I will argue that fruit belongs on pizza 
                using complex statistical models and aggressive hand gestures. Rates: One slice of pizza per hour.
            </p>
        </div>

        <div class="ad-item">
            <div class="ad-header">
                <span class="ad-category">LOST & FOUND</span>
                <span class="ad-price">REWARD</span>
            </div>
            <h3 class="ad-title-small">LOST: SIGNIFICANT DIGIT</h3>
            <p class="ad-description-small">
                Last seen in a rounding error near the NBA standings view. Reward if found and returned to the nearest integer.
            </p>
        </div>
    </div>
</div>

<style>
    .classifieds-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
        background: transparent; /* Show global newspaper texture */
    }

    .classifieds-header {
        text-align: center;
        border-bottom: 4px double #2b2b2b;
        margin-bottom: 30px;
        padding-bottom: 10px;
    }

    .classifieds-header h1 {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        font-weight: 900;
        margin: 0;
        letter-spacing: -2px;
    }

    .classifieds-header .highlight {
        font-style: italic;
        color: #c0392b;
    }

    .classifieds-header .subtitle {
        font-family: 'Roboto Mono', monospace;
        font-size: 0.9rem;
        color: #666;
        text-transform: uppercase;
        margin-top: 5px;
    }

    .ads-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
    }

    .ad-item {
        border: 1px solid #2b2b2b;
        padding: 15px;
        position: relative;
        display: flex;
        flex-direction: column;
        background: rgba(255,255,255,0.3);
    }

    .featured-ad {
        grid-column: 1 / -1; /* Span full width */
        border-width: 2px;
        background: rgba(255,255,255,0.5);
    }

    .ad-header {
        display: flex;
        justify-content: space-between;
        font-family: 'Roboto', sans-serif;
        font-size: 0.75rem;
        font-weight: 700;
        color: #c0392b;
        margin-bottom: 10px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }

    .ad-category {
        letter-spacing: 1px;
    }

    .ad-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.8rem;
        line-height: 1;
        margin: 0 0 15px 0;
        font-weight: 900;
    }

    .ad-title-small {
        font-family: 'Playfair Display', serif;
        font-size: 1.2rem;
        margin: 0 0 10px 0;
        font-weight: 900;
    }

    .ad-description {
        font-family: 'Times New Roman', serif;
        font-size: 1.1rem;
        line-height: 1.4;
        margin-bottom: 15px;
        text-align: justify;
    }

    .ad-description-small {
        font-family: 'Times New Roman', serif;
        font-size: 1rem;
        line-height: 1.3;
        margin-bottom: 10px;
    }

    .ad-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px dashed #999;
    }

    .ad-condition {
        font-size: 0.8rem;
        font-style: italic;
        color: #666;
    }

    .contact-btn {
        background: #2b2b2b;
        color: #f4f1ea;
        text-decoration: none;
        padding: 5px 15px;
        font-family: 'Roboto Mono', monospace;
        font-weight: 700;
        font-size: 0.8rem;
        transition: background 0.2s;
    }

    .contact-btn:hover {
        background: #c0392b;
    }

    @media (max-width: 600px) {
        .classifieds-header h1 {
            font-size: 2.5rem;
        }
    }
</style>