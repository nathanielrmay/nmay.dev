<?php
namespace lib\db\models\panal\nba;

use PDO;

class db_news
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads news items.
     * @param string|null $date YYYY-MM-DD to filter by specific date, or null for all (with limit)
     * @param int $limit Max records to return
     * @return array
     */
    public function read(?string $date = null, int $limit = 50): array
    {
        $sql = "SELECT * FROM panal.nba.aggregate_rss_news";
        $params = [];

        if ($date) {
            // Assuming pub_date is a timestamp or date column
            $sql .= " WHERE published_at::date = :date";
            $params['date'] = $date;
        }

        $sql .= " ORDER BY published_at DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        if ($date) {
            $stmt->bindValue(':date', $date);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
