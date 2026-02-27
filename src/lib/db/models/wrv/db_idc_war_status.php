<?php
namespace lib\db\models\wrv;

use PDO;
use PDOException;

/**
 * Model for the lookup table: web.wrv.idc_war_status
 */
class db_idc_war_status
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads all tournament statuses.
     * @return array
     */
    public function readAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM web.wrv.idc_war_status ORDER BY pk ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Reads a specific status by its primary key.
     * @param int $pk
     * @return array|false
     */
    public function readById(int $pk)
    {
        $stmt = $this->db->prepare('SELECT * FROM web.wrv.idc_war_status WHERE pk = :pk');
        $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
