<?php
namespace lib\db\models\wrv;

use PDO;
use PDOException;

/**
 * Model for the tournament-restaurant join table: web.wrv.idc_war_places_place
 *
 * FK: fk_idc_war -> idc_war.pk
 * FK: fk_places_place -> places_place.pk
 */
class db_idc_war_places_place
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads all entries for a specific war, joined with place details.
     * @param int $warPk
     * @return array
     */
    public function readByWarPk(int $warPk): array
    {
        $sql = '
            SELECT e.pk as entry_pk, e.fk_idc_war, e.fk_places_place, p.id as google_place_id, p.name as place_name
            FROM web.wrv.idc_war_places_place e
            JOIN web.wrv.places_place p ON e.fk_places_place = p.pk
            WHERE e.fk_idc_war = :war_pk
            ORDER BY e.pk ASC
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':war_pk', $warPk, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Insert an entry linking a war to a place.
     * @param int $warPk
     * @param int $placePk
     * @return int|false
     */
    public function write(int $warPk, int $placePk)
    {
        try {
            $sql = 'INSERT INTO web.wrv.idc_war_places_place (fk_idc_war, fk_places_place) VALUES (:war_pk, :place_pk) RETURNING pk';
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':war_pk', $warPk, PDO::PARAM_INT);
            $stmt->bindParam(':place_pk', $placePk, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (int)$row['pk'] : false;
        } catch (PDOException $e) {
            error_log("db_idc_war_places_place write error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a single entry by PK.
     * @param int $pk
     * @return bool
     */
    public function deleteEntry(int $pk): bool
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM web.wrv.idc_war_places_place WHERE pk = :pk');
            $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("db_idc_war_places_place delete error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete ALL entries for a given war (used before re-creating from JS array).
     * @param int $warPk
     * @return bool
     */
    public function deleteByWarPk(int $warPk): bool
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM web.wrv.idc_war_places_place WHERE fk_idc_war = :war_pk');
            $stmt->bindParam(':war_pk', $warPk, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("db_idc_war_places_place deleteByWarPk error: " . $e->getMessage());
            return false;
        }
    }
}
