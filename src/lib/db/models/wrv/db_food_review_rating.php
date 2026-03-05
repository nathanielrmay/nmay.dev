<?php
namespace lib\db\models\wrv;

use PDO;
use PDOException;

class db_food_review_rating
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function readByFoodReviewPk(int $foodReviewPk)
    {
        $sql = 'SELECT * FROM web.wrv.food_review_rating WHERE fk_food_review = :fk_food_review';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':fk_food_review', $foodReviewPk, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function write(int $foodReviewPk, array $scores)
    {
        $sql = '
            INSERT INTO web.wrv.food_review_rating 
            (fk_food_review, rating_product, rating_value, rating_service, rating_atmosphere) 
            VALUES 
            (:fk_food_review, :rating_product, :rating_value, :rating_service, :rating_atmosphere)
            RETURNING pk
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':fk_food_review', $foodReviewPk, PDO::PARAM_INT);
        
        $p = isset($scores['rating_product']) ? (float)$scores['rating_product'] : null;
        $v = isset($scores['rating_value']) ? (float)$scores['rating_value'] : null;
        $s = isset($scores['rating_service']) ? (float)$scores['rating_service'] : null;
        $a = isset($scores['rating_atmosphere']) ? (float)$scores['rating_atmosphere'] : null;

        $stmt->bindValue(':rating_product', $p, $p === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':rating_value', $v, $v === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':rating_service', $s, $s === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindValue(':rating_atmosphere', $a, $a === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        try {
            $execResult = $stmt->execute();
            if (!$execResult) {
                // PDO is likely in silent mode, so execute() just returned false.
                $err = $stmt->errorInfo();
                return "DB ERROR (silent): " . print_r($err, true);
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['pk'] : false;
        } catch (PDOException $e) {
            return "DB ERROR (exception): " . $e->getMessage();
        }
    }
}