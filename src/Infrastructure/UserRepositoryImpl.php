<?php

require_once __DIR__ . '/../Domain/Repository/UserRepository.php';
require_once __DIR__ . '/../Domain/Entity/User.php';
require_once __DIR__ . '/pdo.php';

class UserRepositoryImpl implements UserRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance();
    }

    public function findById(int $id): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        return new User($row['id'], $row['name'], $row['email'], $row['password']);
    }

    public function findByEmail(string $email): ?User {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        return new User($row['id'], $row['name'], $row['email'], $row['password']);
    }

    public function save(User $user): void {
        if ($user->getId() === null) {
            // 新規作成
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$user->getName(), $user->getEmail(), $user->getPassword()]);
            $user->setId($this->pdo->lastInsertId());
        } else {
            // 更新
            $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $stmt->execute([$user->getName(), $user->getEmail(), $user->getPassword(), $user->getId()]);
        }
    }
}