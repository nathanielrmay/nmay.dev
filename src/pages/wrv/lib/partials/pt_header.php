<div class="pt-header">
<div class="wrv-nav">
    <button class="menu-toggle" aria-label="Toggle Menu" onclick="document.getElementById('main-menu').classList.toggle('open')">
        &#9776;
    </button>
    <a href="https://nmay.dev">nmay.dev</a>
    <span class="sep">|</span>
    <a href="/wrv/food/pg_index.php">Food</a>
    <span class="sep">|</span>
    <span style="margin-left: auto; font-size: 0.8em; align-self: center;">
        <span style="margin-left: auto; font-size: 0.65em; align-self: center;">today is </span>
        <span id="local-date"></span>
    </span>
</div>
<script>
    document.getElementById('local-date').textContent = new Date().toLocaleDateString(undefined, {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
</script>
</div>
