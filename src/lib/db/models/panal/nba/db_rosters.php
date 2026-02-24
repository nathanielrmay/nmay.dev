<?php
namespace lib\db\models\panal\nba;

use PDO;

class db_rosters
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads the roster for a specific team.
     * @param int|string $team_id
     * @return array
     */
    public function readByTeamId($team_id): array
    {
        $stmt = $this->db->prepare('SELECT * FROM panal.nba.rosters WHERE team_id = :team_id ORDER BY player');
        $stmt->execute(['team_id' => $team_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
