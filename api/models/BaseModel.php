<?php
// ============================================================
// Base Model
// Web-Based Crop Insurance System
// Shared database query methods for all models
// ============================================================

abstract class BaseModel {
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Find a single record by primary key
     */
    public function find(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Find a single record by a specific column
     */
    public function findBy(string $column, mixed $value): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->table}` WHERE `$column` = ? LIMIT 1"
        );
        $stmt->execute([$value]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Get all records with optional WHERE and ORDER
     */
    public function all(string $where = '', array $params = [], string $order = ''): array {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($where) $sql .= " WHERE $where";
        if ($order) $sql .= " ORDER BY $order";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Count rows with optional WHERE
     */
    public function count(string $where = '', array $params = []): int {
        $sql = "SELECT COUNT(*) FROM `{$this->table}`";
        if ($where) $sql .= " WHERE $where";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Paginated select
     */
    public function paginate(int $offset, int $limit, string $where = '', array $params = [], string $order = ''): array {
        $sql = "SELECT * FROM `{$this->table}`";
        if ($where) $sql .= " WHERE $where";
        if ($order) $sql .= " ORDER BY $order";
        $sql .= " LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([...$params, $limit, $offset]);
        return $stmt->fetchAll();
    }

    /**
     * Insert a new record and return the new ID
     */
    public function insert(array $data): int {
        $cols        = implode(', ', array_map(fn($k) => "`$k`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare(
            "INSERT INTO `{$this->table}` ($cols) VALUES ($placeholders)"
        );
        $stmt->execute(array_values($data));
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a record by primary key
     */
    public function update(int $id, array $data): bool {
        $sets = implode(', ', array_map(fn($k) => "`$k` = ?", array_keys($data)));
        $stmt = $this->db->prepare(
            "UPDATE `{$this->table}` SET $sets WHERE `{$this->primaryKey}` = ?"
        );
        return $stmt->execute([...array_values($data), $id]);
    }

    /**
     * Delete a record by primary key
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = ?"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Run a raw query
     */
    public function raw(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Run a raw query and return single row
     */
    public function rawOne(string $sql, array $params = []): ?array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
