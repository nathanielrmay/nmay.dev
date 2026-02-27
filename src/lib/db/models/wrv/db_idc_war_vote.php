<?php
namespace lib\db\models\wrv;

use PDO;

class db_idc_war_vote {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Submit a ranked vote for a war.
     * 
     * @param int $warPk 
     * @param int|null $userPk
     * @param array $rankings Array of place PKs in ranked order [1st, 2nd, 3rd...]
     * @return int|false The new vote PK or false on failure
     */
    public function write(int $warPk, ?int $userPk, array $rankings) {
        $sql = "INSERT INTO wrv.idc_war_vote (fk_idc_war, fk_user, rankings) 
                VALUES (:war_pk, :user_pk, :rankings)
                RETURNING pk";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':war_pk', $warPk, PDO::PARAM_INT);
        $stmt->bindValue(':user_pk', $userPk, $userPk === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':rankings', json_encode($rankings));
        
        if ($stmt->execute()) {
            return $stmt->fetchColumn();
        }
        return false;
    }

    /**
     * Get all votes for a specific war.
     * 
     * @param int $warPk
     * @return array
     */
    public function readByWarPk(int $warPk) {
        $sql = "SELECT * FROM wrv.idc_war_vote WHERE fk_idc_war = :war_pk ORDER BY created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':war_pk' => $warPk]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
