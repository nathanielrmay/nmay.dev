<?php
namespace lib\db\models\wrv;

use PDO;
use PDOException;

/**
 * Model for the food_review_type table: web.wrv.food_review_type
 */
class db_food_review_type
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads all review types (genres).
     * @return array
     */
    public function readAll(): array
    {
        $sql = 'SELECT * FROM web.wrv.food_review_type ORDER BY name ASC';
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
