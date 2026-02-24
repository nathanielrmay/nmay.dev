<footer class="nba-footer">
    <div class="footer-content">
        <span class="thanks-label">Thanks to:</span>
        <a href="https://www.nba.com/stats/" target="_blank" class="nba-link">NBA STATS API</a>
        <span class="thanks-label"> - AND - </span>
        <a href="https://hoopr.sportsdataverse.org/index.html" target="_blank" title="HoopR Project" class="hoopr-link">
            <img src="https://hoopr.sportsdataverse.org/logo.png" alt="HoopR" class="hoopr-logo-small">
        </a>
    </div>
</footer>

<style>
    .nba-footer {
        /*margin-top: 10px;*/
        padding: 5px 0;
        border-top: 1px solid #ccc;
        font-family: 'Roboto', sans-serif;
        font-size: 0.75rem;
        color: #444;
        background: transparent !important;
        line-height: 1.1 !important; /* Compact line height */
    }

    .footer-content {
        display: flex;
        align-items: center;
        gap: 10px;
        background: transparent !important;
    }

    .thanks-label {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.7rem;
        color: #666;
    }

    .nba-link {
        color: #c0392b;
        text-decoration: none;
        font-weight: 700;
    }

    .nba-link:hover {
        text-decoration: underline;
    }

    .sep {
        color: #ccc;
        font-weight: 300;
    }

    .hoopr-logo-small {
        height: 50px;
        width: auto;
        vertical-align: middle;
        background: transparent !important;
        /* Reset aggressive global img styles from terminal.css */
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .hoopr-link {
        display: flex;
        align-items: center;
        background: transparent !important;
    }
</style>