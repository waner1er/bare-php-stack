<?php

namespace App\Domain\Abstract;

use App\Infrastructure\Database\Database;



abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';

    public static function all(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT * FROM ' . static::$table);
        return $stmt->fetchAll();
    }

    public static function find($id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function save(): bool
    {
        $pdo = Database::getConnection();
        $props = get_object_vars($this);
        unset($props['table'], $props['primaryKey']);
        if (isset($this->{static::$primaryKey})) {
            // Update
            $fields = array_map(fn($k) => "$k = :$k", array_keys($props));
            $sql = 'UPDATE ' . static::$table . ' SET ' . implode(', ', $fields) . ' WHERE ' . static::$primaryKey . ' = :' . static::$primaryKey;
            $props[static::$primaryKey] = $this->{static::$primaryKey};
        } else {
            // Insert
            $columns = implode(', ', array_keys($props));
            $placeholders = implode(', ', array_map(fn($k) => ":$k", array_keys($props)));
            $sql = 'INSERT INTO ' . static::$table . " ($columns) VALUES ($placeholders)";
        }
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($props);
        if (!isset($this->{static::$primaryKey})) {
            $this->{static::$primaryKey} = $pdo->lastInsertId();
        }
        return $result;
    }

    public function delete(): bool
    {
        $pdo = Database::getConnection();
        $sql = 'DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = ?';
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$this->{static::$primaryKey}]);
    }
}
