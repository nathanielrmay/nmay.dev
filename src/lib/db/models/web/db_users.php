<?php

namespace lib\db\models\web;

use PDO;
use PDOException;

class db_users
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    private int $pk;
    private string $id;
    private string $password;

    /**
     * Reads all records from the 'users' table.
     * @return array An array of associative arrays representing the records.
     */
    public function readAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM web.admin.users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reads a single record from the 'users' table by its primary key.
     * @param int $pk The primary key value.
     * @return array|false An associative array representing the record, or false if not found.
     */
    public function read(int $pk)
    {
        $stmt = $this->db->prepare('SELECT * FROM web.admin.users WHERE pk = :pk');
        $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Reads a single record from the 'users' table by its ID and password (verifying hash).
     * @param string $id The user ID.
     * @param string $password The user password.
     * @return array|false An associative array representing the record, or false if not found/invalid.
     */
    public function readByIdPassword(string $id, string $password)
    {
        $stmt = $this->db->prepare('SELECT * FROM web.admin.users WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Creates a new record in the 'users' table with a hashed password.
     * @param array $data An associative array of data for the new record.
     * @return int|false The ID of the newly inserted record on success, false on failure.
     */
    public function write(array $data)
    {
        $sql = 'INSERT INTO web.admin.users (id, password, disabled) VALUES (:id, :password, :disabled)';
        $stmt = $this->db->prepare($sql);
        
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $disabled = isset($data['disabled']) ? (bool)$data['disabled'] : false;
        
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':disabled', $disabled, PDO::PARAM_BOOL);
        try {
            $success = $stmt->execute();
            if ($success) {
                return (int)$this->db->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            // Log error: $e->getMessage();
            return false;
        }
    }

    /**
     * Updates an existing record in the 'users' table.
     * Only updates password if a new one is provided.
     * @param array $data An associative array of data for the record, including the primary key.
     * @return bool True on success, false on failure.
     */
    public function edit(array $data): bool
    {
        $disabled = isset($data['disabled']) ? (bool)$data['disabled'] : false;

        if (!empty($data['password'])) {
            $sql = 'UPDATE web.admin.users SET id = :id, password = :password, disabled = :disabled WHERE pk = :pk';
            $stmt = $this->db->prepare($sql);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            $sql = 'UPDATE web.admin.users SET id = :id, disabled = :disabled WHERE pk = :pk';
            $stmt = $this->db->prepare($sql);
        }

        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':disabled', $disabled, PDO::PARAM_BOOL);
        $stmt->bindParam(':pk', $data['pk'], PDO::PARAM_INT);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error: $e->getMessage();
            return false;
        }
    }

    /**
     * Deletes a record from the 'users' table by its primary key.
     * @param int $pk The primary key value.
     * @return bool True on success, false on failure.
     */
    public function delete(int $pk): bool
    {
        $stmt = $this->db->prepare('DELETE FROM web.admin.users WHERE pk = :pk');
        $stmt->bindParam(':pk', $pk, PDO::PARAM_INT);
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

}
