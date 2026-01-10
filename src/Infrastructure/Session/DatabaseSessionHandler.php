<?php

namespace App\Infrastructure\Session;

use PDO;
use SessionHandlerInterface;

class DatabaseSessionHandler implements SessionHandlerInterface
{
    private PDO $pdo;
    private int $lifetime;

    public function __construct(PDO $pdo, int $lifetime = 7200) // 2 heures par défaut
    {
        $this->pdo = $pdo;
        $this->lifetime = $lifetime;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $stmt = $this->pdo->prepare(
            'SELECT payload FROM sessions WHERE id = :id AND last_activity >= :expiration LIMIT 1'
        );

        $stmt->execute([
            'id' => $id,
            'expiration' => time() - $this->lifetime
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? base64_decode($result['payload']) : '';
    }

    public function write(string $id, string $data): bool
    {
        $payload = base64_encode($data);
        $lastActivity = time();
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Récupérer l'user_id depuis les données de session si disponible
        $sessionData = [];
        if ($data) {
            $sessionData = @unserialize($data) ?: [];
        }
        $userId = $sessionData['user_id'] ?? null;

        $stmt = $this->pdo->prepare(
            'INSERT INTO sessions (id, user_id, payload, last_activity, ip_address, user_agent) 
             VALUES (:id, :user_id, :payload, :last_activity, :ip_address, :user_agent)
             ON DUPLICATE KEY UPDATE 
                user_id = VALUES(user_id),
                payload = VALUES(payload),
                last_activity = VALUES(last_activity),
                ip_address = VALUES(ip_address),
                user_agent = VALUES(user_agent)'
        );

        return $stmt->execute([
            'id' => $id,
            'user_id' => $userId,
            'payload' => $payload,
            'last_activity' => $lastActivity,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);
    }

    public function destroy(string $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function gc(int $max_lifetime): int|false
    {
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE last_activity < :expiration');
        $stmt->execute(['expiration' => time() - $max_lifetime]);
        return $stmt->rowCount();
    }

    /**
     * Nettoie toutes les sessions expirées
     */
    public function clean(): int
    {
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE last_activity < :expiration');
        $stmt->execute(['expiration' => time() - $this->lifetime]);
        return $stmt->rowCount();
    }

    /**
     * Détruit toutes les sessions d'un utilisateur (utile pour déconnexion de tous les appareils)
     */
    public function destroyUserSessions(int $userId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM sessions WHERE user_id = :user_id');
        return $stmt->execute(['user_id' => $userId]);
    }
}
