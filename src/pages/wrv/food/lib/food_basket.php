<?php
namespace pages\wrv\food\lib;

class food_basket {
    /**
     * Calculates the overall average rating for a food review dynamically.
     * Expects an array containing at least the metric columns (rating_product, rating_value, etc).
     * 
     * @param array $reviewRow A row map from db_food_review_rating (or joined queries).
     * @return float|null The computed average, or null if no ratings exist.
     */
    public static function calculateOverallRating(array $reviewRow): ?float {
        $components = [
            'rating_product',
            'rating_value',
            'rating_service',
            'rating_atmosphere'
        ];

        $totalScore = 0.0;
        $componentCount = 0;

        foreach ($components as $key) {
            if (isset($reviewRow[$key]) && is_numeric($reviewRow[$key])) {
                $totalScore += (float)$reviewRow[$key];
                $componentCount++;
            }
        }

        if ($componentCount === 0) {
            return null;
        }

        return $totalScore / $componentCount;
    }
}