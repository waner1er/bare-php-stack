<?php

namespace App\Domain\Abstract;

use PDO;
use App\Infrastructure\Database\Database;

abstract class Model
{
    protected static string $table;
    protected static string $primaryKey = 'id';


    public static function db(): PDO
    {
        return Database::getConnection();
    }

    public static function all(): array
    {
        $stmt = static::db()->query('SELECT * FROM ' . static::$table);
        $results = $stmt->fetchAll();
        return array_map(fn($row) => new static($row), $results);
    }

    public static function find($id): ?static
    {
        $stmt = static::db()->prepare('SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ? new static($result) : null;
    }

    public static function findBySlug(string $slug): ?static
    {
        $stmt = static::db()->prepare('SELECT * FROM ' . static::$table . ' WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();
        return $result ? new static($result) : null;
    }

    public static function findBy(string $field, $value): ?static
    {
        $stmt = static::db()->prepare('SELECT * FROM ' . static::$table . ' WHERE ' . $field . ' = :value LIMIT 1');
        $stmt->execute(['value' => $value]);
        $result = $stmt->fetch();
        return $result ? new static($result) : null;
    }

    public function save(): bool
    {
        $db = static::db();
        $props = get_object_vars($this);
        unset($props['table'], $props['primaryKey']);

        foreach ($props as $key => $value) {
            if (is_bool($value)) {
                $props[$key] = (int) $value;
            }
        }

        if (isset($this->{static::$primaryKey})) {
            $fields = array_map(fn($k) => "$k = :$k", array_keys($props));
            $sql = 'UPDATE ' . static::$table . ' SET ' . implode(', ', $fields) . ' WHERE ' . static::$primaryKey . ' = :' . static::$primaryKey;
            $props[static::$primaryKey] = $this->{static::$primaryKey};
        } else {
            $columns = implode(', ', array_keys($props));
            $placeholders = implode(', ', array_map(fn($k) => ":$k", array_keys($props)));
            $sql = 'INSERT INTO ' . static::$table . " ($columns) VALUES ($placeholders)";
        }

        $stmt = $db->prepare($sql);
        $result = $stmt->execute($props);

        if (!isset($this->{static::$primaryKey})) {
            $this->{static::$primaryKey} = $db->lastInsertId();
        }

        return $result;
    }

    public function delete(): bool
    {
        $sql = 'DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = ?';
        $stmt = static::db()->prepare($sql);
        return $stmt->execute([$this->{static::$primaryKey}]);
    }

    public function toArray(): array
    {
        $props = get_object_vars($this);
        unset($props['table'], $props['primaryKey']);
        return $props;
    }

    public function hasMany(string $relatedClass, string $foreignKey, string $localKey = 'id'): array
    {
        $stmt = static::db()->prepare(
            'SELECT * FROM ' . $relatedClass::$table . ' WHERE ' . $foreignKey . ' = :value',
        );
        $stmt->execute(['value' => $this->{$localKey}]);
        $results = $stmt->fetchAll();
        return array_map(fn($row) => new $relatedClass($row), $results);
    }

    public function belongsTo(string $relatedClass, string $foreignKey, string $ownerKey = 'id'): ?object
    {
        $stmt = static::db()->prepare(
            'SELECT * FROM ' . $relatedClass::$table . ' WHERE ' . $ownerKey . ' = :value LIMIT 1',
        );
        $stmt->execute(['value' => $this->{$foreignKey}]);
        $result = $stmt->fetch();
        return $result ? new $relatedClass($result) : null;
    }
}
