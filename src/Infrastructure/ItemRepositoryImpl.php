<?php

require_once __DIR__ . '/../Domain/Repository/ItemRepository.php';
require_once __DIR__ . '/../Domain/Entity/Item.php';
require_once __DIR__ . '/../Domain/ValueObject/ItemName.php';
require_once __DIR__ . '/../Domain/ValueObject/TagId.php';
require_once __DIR__ . '/pdo.php';

class ItemRepositoryImpl implements ItemRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance();
    }

    public function findByUser(int $userId): array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM items WHERE user_id = ?");
            $stmt->execute([$userId]);
            $rows = $stmt->fetchAll();
            
            $items = [];
            foreach ($rows as $row) {
                $items[] = $this->createItemFromRow($row);
            }
            
            return $items;
        } catch (PDOException $e) {
            throw new RuntimeException("ユーザーのアイテム検索中にデータベースエラーが発生しました: " . $e->getMessage(), 0, $e);
        }
    }

    public function findByTagId(string $tagId): ?Item {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM items WHERE tag_id = ?");
            $stmt->execute([$tagId]);
            $row = $stmt->fetch();
            
            if (!$row) {
                return null;
            }
            
            return $this->createItemFromRow($row);
        } catch (PDOException $e) {
            throw new RuntimeException("タグIDによるアイテム検索中にデータベースエラーが発生しました: " . $e->getMessage(), 0, $e);
        }
    }

    public function findById(int $id): ?Item {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM items WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            
            if (!$row) {
                return null;
            }
            
            return $this->createItemFromRow($row);
        } catch (PDOException $e) {
            throw new RuntimeException("アイテム検索中にデータベースエラーが発生しました: " . $e->getMessage(), 0, $e);
        }
    }

    public function save(Item $item, int $userId = null): void {
        $this->pdo->beginTransaction();
        
        try {
            if ($item->getId() === null) {
                // 新規作成
                if ($userId === null) {
                    throw new InvalidArgumentException("新規アイテム作成時はuser_idが必要です");
                }
                $stmt = $this->pdo->prepare("INSERT INTO items (user_id, name, tag_id, image) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $userId,
                    $item->getName()->getName(),
                    $item->getTagId() ? $item->getTagId()->getId() : null,
                    $item->getImage()
                ]);
                $item->setId($this->pdo->lastInsertId());
            } else {
                // 更新
                $stmt = $this->pdo->prepare("UPDATE items SET name = ?, tag_id = ?, image = ? WHERE id = ?");
                $stmt->execute([
                    $item->getName()->getName(),
                    $item->getTagId() ? $item->getTagId()->getId() : null,
                    $item->getImage(),
                    $item->getId()
                ]);
            }
            
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            
            // 一意制約違反の場合（タグIDの重複）
            if ($e->getCode() == 23000 || strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                throw new RuntimeException("このタグIDは既に使用されています", 0, $e);
            }
            
            // 外部キー制約違反の場合
            if (strpos($e->getMessage(), 'FOREIGN KEY constraint failed') !== false) {
                throw new RuntimeException("指定されたユーザーが存在しません", 0, $e);
            }
            
            throw new RuntimeException("アイテム保存中にデータベースエラーが発生しました: " . $e->getMessage(), 0, $e);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new RuntimeException("アイテム保存中に予期しないエラーが発生しました: " . $e->getMessage(), 0, $e);
        }
    }

    public function createItemFromRow(array $row): Item {
        try {
            $name = new ItemName($row['name']);
            $tagId = $row['tag_id'] ? new TagId($row['tag_id']) : null;
            
            return new Item($row['id'], $name, $tagId, $row['image']);
        } catch (Exception $e) {
            throw new RuntimeException("アイテムデータの変換中にエラーが発生しました: " . $e->getMessage(), 0, $e);
        }
    }
}