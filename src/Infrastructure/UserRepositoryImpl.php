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
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            
            if (!$row) {
                return null;
            }
            
            return new User($row['id'], $row['name'], $row['email'], $row['password']);
        } catch (PDOException $e) {
            throw new RuntimeException("ユーザー検索中にデータベースエラーが発生しました: " . $e->getMessage(), 0, $e);
        }
    }

    public function findByEmail(string $email): ?User {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $row = $stmt->fetch();
            
            if (!$row) {
                return null;
            }
            
            return new User($row['id'], $row['name'], $row['email'], $row['password']);
        } catch (PDOException $e) {
            throw new RuntimeException("メールアドレスによるユーザー検索中にデータベースエラーが発生しました: " . $e->getMessage(), 0, $e);
        }
    }

    public function save(User $user): void {
        $this->pdo->beginTransaction();
        
        try {
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
            
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            
            // 一意制約違反の場合は特別なメッセージ
            if ($e->getCode() == 23000 || strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                throw new RuntimeException("このメールアドレスは既に使用されています", 0, $e);
            }
            
            throw new RuntimeException("ユーザー保存中にデータベースエラーが発生しました: " . $e->getMessage(), 0, $e);
        }
    }
}