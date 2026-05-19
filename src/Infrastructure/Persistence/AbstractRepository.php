<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Infrastructure\Database\Database;
use PDO;

abstract class AbstractRepository
{
    protected string $table;
    protected string $primaryKey = 'id';
    /** @var class-string */
    protected string $entityClass;

    protected function db(): PDO
    {
        return Database::getConnection();
    }

    protected function hydrate(array $row): object
    {
        $class = $this->entityClass;
        return new $class($row);
    }

    /** @return object[] */
    public function findAllRaw(): array
    {
        $stmt = $this->db()->query('SELECT * FROM ' . $this->table);
        return array_map(fn($row) => $this->hydrate($row), $stmt->fetchAll());
    }

    public function findOneRaw(int|string $id): ?object
    {
        $stmt = $this->db()->prepare(
            'SELECT * FROM ' . $this->table . ' WHERE ' . $this->primaryKey . ' = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    protected function findOneBy(string $field, mixed $value): ?object
    {
        $stmt = $this->db()->prepare(
            'SELECT * FROM ' . $this->table . ' WHERE ' . $field . ' = :value LIMIT 1'
        );
        $stmt->execute(['value' => $value]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    /** @return object[] */
    protected function findManyBy(string $field, mixed $value): array
    {
        $stmt = $this->db()->prepare(
            'SELECT * FROM ' . $this->table . ' WHERE ' . $field . ' = :value'
        );
        $stmt->execute(['value' => $value]);
        return array_map(fn($row) => $this->hydrate($row), $stmt->fetchAll());
    }

    protected function persist(object $entity): bool
    {
        $db = $this->db();
        $props = $this->extractProps($entity);

        $pk = $this->primaryKey;
        $hasPk = isset($entity->{$pk}) && $entity->{$pk};

        if ($hasPk) {
            $fields = array_map(fn($k) => "$k = :$k", array_keys($props));
            $sql = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $fields)
                . ' WHERE ' . $pk . ' = :' . $pk;
            $props[$pk] = $entity->{$pk};
        } else {
            $columns = implode(', ', array_keys($props));
            $placeholders = implode(', ', array_map(fn($k) => ":$k", array_keys($props)));
            $sql = 'INSERT INTO ' . $this->table . " ($columns) VALUES ($placeholders)";
        }

        $stmt = $db->prepare($sql);
        $result = $stmt->execute($props);

        if (!$hasPk) {
            $entity->{$pk} = (int) $db->lastInsertId();
        }

        return $result;
    }

    protected function remove(object $entity): bool
    {
        $pk = $this->primaryKey;
        $stmt = $this->db()->prepare(
            'DELETE FROM ' . $this->table . ' WHERE ' . $pk . ' = ?'
        );
        return $stmt->execute([$entity->{$pk}]);
    }

    /**
     * Extrait les colonnes persistables de l'entité.
     * Par défaut : toutes les propriétés publiques scalaires/null.
     * Les sous-classes peuvent override pour filtrer les relations hydratées.
     */
    protected function extractProps(object $entity): array
    {
        $props = get_object_vars($entity);
        foreach ($props as $key => $value) {
            if (is_object($value) || is_array($value)) {
                unset($props[$key]);
                continue;
            }
            if (is_bool($value)) {
                $props[$key] = (int) $value;
            }
        }
        return $props;
    }
}
