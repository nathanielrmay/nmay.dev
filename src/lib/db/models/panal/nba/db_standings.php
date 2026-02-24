<?php
namespace lib\db\models\panal\nba;

use PDO;

class db_standings
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads all records from the standings table.
     * @return array
     */
    public function readAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM panal.nba.standings ORDER BY conference, win_pct DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reads standings for a specific team.
     * @param int|string $id
     * @return array|false
     */
    public function readByTeamId($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM panal.nba.standings WHERE team_id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Reads standings for a specific division.
     * @param string $division
     * @return array
     */
    public function readByDivision($division): array
    {
        $stmt = $this->db->prepare('SELECT * FROM panal.nba.standings WHERE division = :division ORDER BY win_pct DESC');
        $stmt->execute(['division' => $division]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reads standings for a specific conference.
     * @param string $conference
     * @return array
     */
    public function readByConference($conference): array
    {
        $stmt = $this->db->prepare('SELECT * FROM panal.nba.standings WHERE conference = :conference ORDER BY win_pct DESC');
        $stmt->execute(['conference' => $conference]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
