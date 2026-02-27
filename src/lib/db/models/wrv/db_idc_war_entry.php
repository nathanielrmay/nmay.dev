<?php
namespace lib\db\models\wrv;

use PDO;
use PDOException;

/**
 * Model for the many-to-many tournament participants table: web.wrv.idc_war_entry
 */
class db_idc_war_entry
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads all entries (restaurants) for a specific tournament, including place details.
     * @param int $warPk
     * @return array
     */
    public function readEntriesForWar(int $warPk): array
    {
        $sql = '
            SELECT e.pk as entry_pk, e.fk_idc_war, e.fk_places_place, p.id as google_place_id, p.name as place_name
            FROM web.wrv.idc_war_entry e
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
     * Writes (insert or update) an entry record.
     * @param array $data Associative array of column => value
     * @return int|false Returns the primary key on success, false on failure.
     */
    public function write(array $data)
    {
        try {
            if (isset($data['pk']) && !empty($data['pk'])) {
                // Update existing record
                $fields = [];
                $params = [];
                foreach ($data as $key => $value) {
                    if ($key === 'pk') continue;
                    $fields[] = "{$key} = :{$key}";
                    $params[":{$key}"] = $value;
                }
                $params[':pk'] = $data['pk'];

                $sql = 'UPDATE web.wrv.idc_war_entry SET ' . implode(', ', $fields) . ' WHERE pk = :pk';
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute($params);

                return $result ? (int)$data['pk'] : false;
            } else {
                // Insert new record
                $columns = array_keys($data);
                $placeholders = array_map(fn($col) => ":$col", $columns);
                $params = [];
                foreach ($data as $key => $value) {
                    $params[":{$key}"] = $value;
                }

                $sql = sprintf(
                    'INSERT INTO web.wrv.idc_war_entry (%s) VALUES (%s) RETURNING pk',
                    implode(', ', $columns),
                    implode(', ', $placeholders)
                );

                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row ? (int)$row['pk'] : false;
            }
        } catch (PDOException $e) {
            error_log("db_idc_war_entry write error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Removes an entry from a tournament.
     * @param int $pk
     * @return bool
     */
    public function deleteEntry(int $pk): bool
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM web.wrv.idc_war_entry WHERE pk = :pk');
            $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("db_idc_war_entry delete error: " . $e->getMessage());
            return false;
        }
    }
}
