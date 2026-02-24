<?php
namespace pages\sports_anals\nba\lib;

/**
 * NBA-specific utility class (Local Basket)
 * Provides helper methods for NBA pages and partials.
 */
class basket {
    /**
     * Calculates the contrast color (black or white) for a given hex color.
     * Based on the YIQ color space.
     *
     * @param string $hex The hex color code (e.g., "#333", "ffffff")
     * @return string The contrast color ("#000" or "#fff")
     */
    public static function getContrastColorTeam($hex) {
        $hex = ltrim($hex, '#');
        if (strlen($hex) == 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        if (!ctype_xdigit($hex) || strlen($hex) != 6) return '#000';
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        return ($yiq >= 128) ? '#000' : '#fff';
    }
}
