<?php
namespace lib\db\models\wrv;

use PDO;
use PDOException;

/**
 * Model for the cached places table: web.wrv.places_place_cache
 *
 * Cached data refreshes every 30 days (or per Google Places API TOS).
 * Each row links to a permanent place via place_pk.
 *
 * Columns: pk (auto), place_pk (FK to places_place), cached_at (timestamp)
 * Additional data columns will be added as caching needs are defined.
 */
class db_places_place_cache
{
    private PDO $db;

    /** @var int Default max age in days before cache is considered stale */
    private int $maxAgeDays = 30;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Reads cached data for a given place.
     * @param int $placePk FK to places_place
     * @return array|false
     */
    public function readByPlacePk(int $placePk)
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM web.wrv.places_place_cache WHERE place_pk = :place_pk'
        );
        $stmt->bindParam(':place_pk', $placePk, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Checks whether the cached data for a place is stale.
     * @param int $placePk    FK to places_place
     * @param int $maxAgeDays Override default max age (default: 30)
     * @return bool           True if cache is missing or older than $maxAgeDays
     */
    public function isStale(int $placePk, int $maxAgeDays = 0): bool
    {
        if ($maxAgeDays <= 0) {
            $maxAgeDays = $this->maxAgeDays;
        }

        $cached = $this->readByPlacePk($placePk);
        if (!$cached) {
            return true;
        }

        $cachedAt = strtotime($cached['cached_at']);
        $ageInDays = (time() - $cachedAt) / 86400;

        return $ageInDays >= $maxAgeDays;
    }

    /**
     * Inserts or updates cached data for a place.
     * @param array $data ['place_pk' => int, ...additional cached fields]
     * @return int|false  The new pk on success, false on failure
     */
    public function write(array $data)
    {
        $sql = 'INSERT INTO web.wrv.places_place_cache (place_pk, cached_at) VALUES (:place_pk, CURRENT_TIMESTAMP)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':place_pk', $data['place_pk'], PDO::PARAM_INT);

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
     * Deletes cached data for a place (to force refresh).
     * @param int $placePk FK to places_place
     * @return bool
     */
    public function deleteByPlacePk(int $placePk): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM web.wrv.places_place_cache WHERE place_pk = :place_pk'
        );
        $stmt->bindParam(':place_pk', $placePk, PDO::PARAM_INT);
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
