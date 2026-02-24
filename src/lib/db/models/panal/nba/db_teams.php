<?php
namespace lib\db\models\panal\nba;

use PDO;

class db_teams
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads all records from the teams view.
     * @return array
     */
    public function readAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM panal.nba.teams ORDER BY team_city, team_name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reads a single team by ID.
     * @param int|string $id
     * @return array|false
     */
    public function read($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM panal.nba.teams WHERE team_id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
