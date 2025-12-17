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
        $stmt = $this->pdo->prepare("SELECT * FROM items WHERE user_id = ?");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();
        
        $items = [];
        foreach ($rows as $row) {
            $items[] = $this->createItemFromRow($row);
        }
        
        return $items;
    }

    public function findByTagId(string $tagId): ?Item {
        $stmt = $this->pdo->prepare("SELECT * FROM items WHERE tag_id = ?");
        $stmt->execute([$tagId]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        return $this->createItemFromRow($row);
    }

    public function findById(int $id): ?Item {
        $stmt = $this->pdo->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        return $this->createItemFromRow($row);
    }

    public function save(Item $item, int $userId = null): void {
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
    }

    public function createItemFromRow(array $row): Item {
        $name = new ItemName($row['name']);
        $tagId = $row['tag_id'] ? new TagId($row['tag_id']) : null;
        
        return new Item($row['id'], $name, $tagId, $row['image']);
    }
}