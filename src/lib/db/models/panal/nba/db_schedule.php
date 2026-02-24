<?php
namespace lib\db\models\panal\nba;

use PDO;

class db_schedule
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads records for a specific date.
     * @param string|null $date YYYY-MM-DD
     * @return array
     */
    public function readByDate(?string $date = null): array
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }
        
        $sql = "
            SELECT s.*, 
                   h.color AS home_team_color, 
                   h.alternate_color AS home_team_alt_color,
                   a.color AS away_team_color, 
                   a.alternate_color AS away_team_alt_color
            FROM panal.nba.schedule s
            LEFT JOIN panal.nba.teams h ON s.home_team_id::text = h.team_id
            LEFT JOIN panal.nba.teams a ON s.away_team_id::text = a.team_id
            WHERE s.game_date::date = :date 
            ORDER BY s.game_date, s.game_time_est ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reads records for today's games from the schedule view.
     * @return array
     */
    public function readToday(): array
    {
        return $this->readByDate(date('Y-m-d'));
    }

    /**
     * Reads games for a specific team.
     * @param int|string $team_id
     * @param int $limit
     * @param string $scope 'future', 'past', or 'all'
     * @param string $sort 'ASC' or 'DESC' (default depends on scope)
     * @return array
     */
    public function readByTeamId($team_id, $limit = 10, $scope = 'future', $sort = 'ASC'): array
    {
        $today = date('Y-m-d');
        
        $sql = "
            SELECT s.*, 
                   h.color AS home_team_color, 
                   h.alternate_color AS home_team_alt_color,
                   a.color AS away_team_color, 
                   a.alternate_color AS away_team_alt_color
            FROM panal.nba.schedule s
            LEFT JOIN panal.nba.teams h ON s.home_team_id::text = h.team_id
            LEFT JOIN panal.nba.teams a ON s.away_team_id::text = a.team_id
            WHERE (s.home_team_id::text = :team_id OR s.away_team_id::text = :team_id)
        ";

        if ($scope === 'future') {
            $sql .= " AND s.game_date::date >= :today";
        } elseif ($scope === 'past') {
            $sql .= " AND s.game_date::date < :today";
        }

        $sql .= " ORDER BY s.game_date " . ($sort === 'DESC' ? 'DESC' : 'ASC');
        $sql .= " LIMIT :limit";
        echo 'sql'. $sql;

        $stmt = $this->db->prepare($sql);
        $params = ['team_id' => $team_id, 'limit' => $limit];
        if ($scope !== 'all') {
            $params['today'] = $today;
        }
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reads all records from the schedule view.
     * @return array
     */
    public function readAll(): array
    {
        // Limit to upcoming or recent games if needed, but for now select all
        // Assuming standard columns, we'll fetch everything
        $stmt = $this->db->query('SELECT * FROM panal.nba.schedule ORDER BY game_date ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
