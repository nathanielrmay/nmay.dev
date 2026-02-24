<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
require_once __DIR__ . '/lib/basket.php';
use lib\basket;
use lib\content;

$basket = new basket();
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
if ($path == '/' || $path == '/index.php') {
    $path = '/pg_index.php';
}
$document_root = $_SERVER['DOCUMENT_ROOT'];
$pages_request_uri = "/pages" . $path; //page path
$target_file = $document_root . $pages_request_uri; //Path from www/
$is_404 = !file_exists($target_file) && $path != '/' && $path != '/index.php' && $path != '/pg_index.php';
if ($is_404) {
    $pages_request_uri = "/pages/pg_404.php";
}

// Inspect the page (loads class, captures output)
$pageInfo = $basket->inspectPage($pages_request_uri);
$pageObj = $pageInfo['page'];
$pageHtml = $pageInfo['html'];

// Defaults
$pageTitle = "nmay.dev";
$headerContent = new content("/lib/partials/header.php");
$menuContent = new content("/lib/partials/vertical_menu.php");
$footerContent = new content("/lib/partials/footer.php");

// Override if iPage
if ($pageObj) {
    if ($t = $pageObj->getPageTitle()) {
        $pageTitle = $t;
    }

    if ($h = $pageObj->getHeader()) {
        $headerContent = ($h instanceof content) ? $h : new content($h);
    }

    if ($m = $pageObj->getVerticalMenu()) {
        $menuContent = ($m instanceof content) ? $m : new content($m);
    }

    if ($f = $pageObj->getFooter()) {
        $footerContent = ($f instanceof content) ? $f : new content($f);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php if ($pageTitle) echo htmlspecialchars($pageTitle); else echo "pageTitle null"; ?></title>
    <?php
    $cssFiles = ['/lib/nmay.css', '/lib/nmay.mob.css'];
    if ($pageObj && method_exists($pageObj, 'getCss')) {
        $cssFiles = $pageObj->getCss();
    }
    foreach ($cssFiles as $css) {
        echo '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($css) . '">' . "\n";
    }
    ?>
</head>
<body class="<?= ($pageObj) ? htmlspecialchars($pageObj->getBodyClass()) : '' ?>">
<div class="header"> <?php $headerContent->output(); ?> </div>
<div class="vertical_menu" id="main-menu"> <?php $menuContent->output(); ?> </div>

<div class="content">
    <?php echo $pageHtml; ?>
</div>
<div class="footer"> <?php $footerContent->output(); ?> </div>
</body>
