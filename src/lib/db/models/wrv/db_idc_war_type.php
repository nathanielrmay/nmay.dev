<?php
namespace lib\db\models\wrv;

use PDO;
use PDOException;

/**
 * Model for the lookup table: web.wrv.idc_war_type
 */
class db_idc_war_type
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads all tournament types.
     * @return array
     */
    public function readAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM web.wrv.idc_war_type ORDER BY pk ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Reads a specific type by its primary key.
     * @param int $pk
     * @return array|false
     */
    public function readById(int $pk)
    {
        $stmt = $this->db->prepare('SELECT * FROM web.wrv.idc_war_type WHERE pk = :pk');
        $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
