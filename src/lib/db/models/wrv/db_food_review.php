<?php
namespace lib\db\models\wrv;

use PDO;
use PDOException;

/**
 * Model for the food_review table: web.wrv.food_review
 */
class db_food_review
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads all reviews, joined with the restaurant name.
     * @param int|string|null $reviewTypePk Optional review type PK or 'Other' to filter by.
     * @return array
     */
    public function readAll($reviewTypePk = null): array
    {
        $sql = '
            SELECT fr.*, p.name as place_name, 
                   frr.rating_product, frr.rating_value, frr.rating_service, frr.rating_atmosphere,
                   c.types, c.formatted_address, c.price_level,
                   frt.name as genre_name
            FROM web.wrv.food_review fr
            JOIN web.wrv.places_place p ON fr.fk_places_place = p.pk
            LEFT JOIN web.wrv.food_review_rating frr ON fr.pk = frr.fk_food_review
            LEFT JOIN web.wrv.places_cache c ON p.pk = c.fk_places_place
            LEFT JOIN web.wrv.food_review_type frt ON fr.fk_review_type = frt.pk
        ';

        $params = [];
        if ($reviewTypePk !== null && $reviewTypePk !== '' && strtolower((string)$reviewTypePk) !== 'both' && strtolower((string)$reviewTypePk) !== 'all') {
            if (strtolower((string)$reviewTypePk) === 'other') {
                $sql .= ' WHERE fr.fk_review_type IS NULL ';
            }
            else {
                $sql .= ' WHERE fr.fk_review_type = :review_type_pk ';
                $params[':review_type_pk'] = (int)$reviewTypePk;
            }
        }

        $sql .= ' ORDER BY fr.created_at DESC';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Read a specific review.
     * @param int $pk
     * @return array|false
     */
    public function readById(int $pk)
    {
        $sql = '
            SELECT fr.*, p.name as place_name,
                   frt.name as genre_name
            FROM web.wrv.food_review fr
            JOIN web.wrv.places_place p ON fr.fk_places_place = p.pk
            LEFT JOIN web.wrv.food_review_type frt ON fr.fk_review_type = frt.pk
            WHERE fr.pk = :pk
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a new food review.
     * @param int $placesPlacePk
     * @param string $title
     * @param string $reviewText
     * @param string|null $dateVisited (YYYY-MM-DD)
     * @param int|null $userPk
     * @param int|null $reviewTypePk
     * @return int|false
     */
    public function write(int $placesPlacePk, string $title, string $reviewText, ?string $dateVisited = null, ?int $userPk = null, ?int $reviewTypePk = null)
    {
        $sql = '
            INSERT INTO web.wrv.food_review 
            (fk_places_place, title, review_text, date_visited, fk_user, fk_review_type) 
            VALUES 
            (:fk_places_place, :title, :review_text, :date_visited, :fk_user, :fk_review_type)
            RETURNING pk
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':fk_places_place', $placesPlacePk, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':review_text', $reviewText);
        $stmt->bindParam(':date_visited', $dateVisited);
        $stmt->bindParam(':fk_user', $userPk, PDO::PARAM_INT);
        $stmt->bindParam(':fk_review_type', $reviewTypePk, PDO::PARAM_INT);

        try {
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['pk'] : false;
        }
        catch (PDOException $e) {
            error_log("db_food_review write error: " . $e->getMessage());
            return false;
        }
    }
}
