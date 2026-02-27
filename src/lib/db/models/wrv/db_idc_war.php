<?php
namespace lib\db\models\wrv;

use PDO;
use PDOException;

/**
 * Model for the main tournament table: web.wrv.idc_war
 */
class db_idc_war
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads a specific tournament by its primary key.
     * @param int $pk
     * @return array|false
     */
    public function readById(int $pk)
    {
        $stmt = $this->db->prepare('SELECT * FROM web.wrv.idc_war WHERE pk = :pk');
        $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Reads all tournaments that are not complete.
     * @return array
     */
    public function readActiveTournaments(): array
    {
        // Assuming 'complete' status is pk 4 as seen in the schema inspection
        $stmt = $this->db->query('SELECT * FROM web.wrv.idc_war WHERE fk_status != 4 ORDER BY deadline ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Writes (insert or update) an IDC war record.
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

                $sql = 'UPDATE web.wrv.idc_war SET ' . implode(', ', $fields) . ' WHERE pk = :pk';
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
                    'INSERT INTO web.wrv.idc_war (%s) VALUES (%s) RETURNING pk',
                    implode(', ', $columns),
                    implode(', ', $placeholders)
                );

                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                return $row ? (int)$row['pk'] : false;
            }
        } catch (PDOException $e) {
            error_log("db_idc_war write error: " . $e->getMessage());
            return false;
        }
    }
}
