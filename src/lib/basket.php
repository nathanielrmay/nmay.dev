<?php

namespace lib;
use lib\contracts\iPage;
use lib\db\db;

class basket
{
    function __construct()
    {
        $this->register_autoloader();
    }

    function register_autoloader()
    {
        spl_autoload_register(function ($stClass) {
            $arrClass = explode('\\', $stClass);
            if ($arrClass[0] == 'lib' || $arrClass[0] == 'pages') {
                $file = str_replace('\\', DIRECTORY_SEPARATOR, $stClass) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
                return false;
            }
        });
    }

    static function pretty_print_array($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }

    /**
     * Renders a partial/file with specific variables extracted into its scope.
     *
     * @param string $path Relative path from src/ (e.g., "/pages/..." or "/lib/...")
     * @param array $args Associative array of variables to make available in the view
     * @return string The rendered HTML
     */
    public static function render(string $path, array $args = []): string
    {
        // extract variables into local scope
        extract($args);

        // normalize path (ensure leading slash is handled relative to document root/src)
        // Assuming structure is X:\projects\dev\public_web\src\
        $fullPath = __DIR__ . '/../' . ltrim($path, '/');

        if (!file_exists($fullPath)) {
            return "<!-- Missing Partial: " . htmlspecialchars($path) . " -->";
        }

        ob_start();
        include $fullPath;
        return ob_get_clean();
    }

    /**
     * Inspects a requested page path and returns the HTML output and Page Object.
     * @param string $relativePath
     * @return array ['html' => string, 'page' => iPage|null]
     */
    public function inspectPage($relativePath)
    {

        // Normalize path to use forward slashes
        $normalizedPath = str_replace('\\', '/', $relativePath);
//          /pages/text_games/fof/docs/media.page.fof.docs.mp_setup/pg_mp_setup.php

        // Remove leading slash if present
        if (strpos($normalizedPath, '/') === 0) {
            $normalizedPath = substr($normalizedPath, 1);
//              echo $normalizedPath;
//              pages/text_games/fof/docs/media.page.fof.docs.mp_setup/pg_mp_setup.php
        }

        $className = null;
        // Extract the portion starting with 'pages/' or 'lib/'
        if (preg_match('/^(pages|lib)\/.*\.php$/', $normalizedPath, $matches)) {
            $matchPath = $matches[0];
//              pages/text_games/fof/docs/media.page.fof.docs.mp_setup/pg_mp_setup.php

            // Convert path to namespace: pages/account/login.php -> pages\account\login
            $className = str_replace(['/', '.php'], ['\\', ''], $matchPath);
//              pages\text_games\fof\docs\media.page.fof.docs.mp_setup\pg_mp_setup
        }

        ob_start();
        $pageObj = null;

        if (file_exists($normalizedPath)) {
            include_once $normalizedPath;
            if ($className && class_exists($className, false)) {
                $interfaces = class_implements($className);
                if ($interfaces && in_array(iPage::class, $interfaces, true)) {
                    $pageObj = new $className();
                }
            }
        }

        $html = ob_get_clean();

        return [
            'html' => $html,
            'page' => $pageObj
        ];
    }

    //<editor-fold desc="Database connections">
    static function db_web()
    {
        $db = new db();
        return $db->getWebDbCon();
    }

    static function db_panal()
    {
        $db = new db();
        return $db->getPanalDbCon();
    }
    //</editor-fold>
}

?>