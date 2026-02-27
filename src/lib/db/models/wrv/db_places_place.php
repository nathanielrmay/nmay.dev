<?php
namespace lib\db\models\wrv;

use PDO;
use PDOException;

/**
 * Model for the permanent places table: web.wrv.places_place
 *
 * Records in this table are never removed. Each row represents a
 * restaurant that has been reviewed or saved.
 *
 * Columns: pk (auto), id (Google place_id), name
 */
class db_places_place
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads all place records.
     * @return array
     */
    public function readAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM web.wrv.places_place ORDER BY name');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reads a single place by primary key.
     * @param int $pk
     * @return array|false
     */
    public function read(int $pk)
    {
        $stmt = $this->db->prepare('SELECT * FROM web.wrv.places_place WHERE pk = :pk');
        $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Reads a single place by its Google place_id.
     * @param string $id Google place_id
     * @return array|false
     */
    public function readById(string $id)
    {
        $stmt = $this->db->prepare('SELECT * FROM web.wrv.places_place WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Inserts a new place record.
     * @param array $data ['id' => string, 'name' => string]
     * @return int|false  The new pk on success, false on failure
     */
    public function write(array $data)
    {
        $sql = 'INSERT INTO web.wrv.places_place (id, name) VALUES (:id, :name)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':name', $data['name']);

        try {
            $success = $stmt->execute();
            if ($success) {
                return (int)$this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Deletes a place record by primary key.
     * Note: This exists for admin use only — normal operation never deletes places.
     * @param int $pk
     * @return bool
     */
    public function delete(int $pk): bool
    {
        $stmt = $this->db->prepare('DELETE FROM web.wrv.places_place WHERE pk = :pk');
        $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
