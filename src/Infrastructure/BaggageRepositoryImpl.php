<?php

require_once __DIR__ . '/../Domain/Repository/BaggageRepository.php';
require_once __DIR__ . '/../Domain/Repository/BaggageItemRepository.php';
require_once __DIR__ . '/../Domain/Entity/Baggage.php';
require_once __DIR__ . '/../Domain/Entity/BaggageItem.php';
require_once __DIR__ . '/pdo.php';
require_once __DIR__ . '/ItemRepositoryImpl.php';

class BaggageRepositoryImpl implements BaggageRepository {
    private PDO $pdo;
    private ItemRepositoryImpl $itemRepository;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance();
        $this->itemRepository = new ItemRepositoryImpl();
    }

    public function findByUserAndDate(int $userId, string $date): array {
        $stmt = $this->pdo->prepare("SELECT * FROM baggages WHERE user_id = ? AND date = ? AND is_template = 0");
        $stmt->execute([$userId, $date]);
        $rows = $stmt->fetchAll();
        
        $baggages = [];
        foreach ($rows as $row) {
            $baggage = new Baggage($row['id'], $row['user_id'], $row['date'], (bool)$row['is_template'], $row['name']);
            $this->loadItems($baggage);
            $baggages[] = $baggage;
        }
        
        return $baggages;
    }

    public function findTemplatesByUser(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM baggages WHERE user_id = ? AND is_template = 1");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();
        
        $templates = [];
        foreach ($rows as $row) {
            $baggage = new Baggage($row['id'], $row['user_id'], $row['date'], (bool)$row['is_template'], $row['name']);
            $this->loadItems($baggage);
            $templates[] = $baggage;
        }
        
        return $templates;
    }

    public function findById(int $id): ?Baggage {
        $stmt = $this->pdo->prepare("SELECT * FROM baggages WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        
        $baggage = new Baggage($row['id'], $row['user_id'], $row['date'], (bool)$row['is_template'], $row['name']);
        $this->loadItems($baggage);
        
        return $baggage;
    }

    public function save(Baggage $baggage): void {
        $this->pdo->beginTransaction();
        
        try {
            if ($baggage->getId() === null) {
                // 新規作成
                $stmt = $this->pdo->prepare("INSERT INTO baggages (user_id, date, is_template, name) VALUES (?, ?, ?, ?)");
                $stmt->execute([
                    $baggage->getUserId(),
                    $baggage->getDate(),
                    $baggage->isTemplate() ? 1 : 0,
                    $baggage->getName()
                ]);
                $baggage->setId($this->pdo->lastInsertId());
            } else {
                // 更新
                $stmt = $this->pdo->prepare("UPDATE baggages SET user_id = ?, date = ?, is_template = ?, name = ? WHERE id = ?");
                $stmt->execute([
                    $baggage->getUserId(),
                    $baggage->getDate(),
                    $baggage->isTemplate() ? 1 : 0,
                    $baggage->getName(),
                    $baggage->getId()
                ]);
            }
            
            // 既存のアイテム関連付けを削除
            $stmt = $this->pdo->prepare("DELETE FROM baggage_items WHERE baggage_id = ?");
            $stmt->execute([$baggage->getId()]);
            
            // 新しいアイテム関連付けを保存
            foreach ($baggage->getItems() as $item) {
                $stmt = $this->pdo->prepare("INSERT INTO baggage_items (baggage_id, item_id) VALUES (?, ?)");
                $stmt->execute([$baggage->getId(), $item->getId()]);
            }
            
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function loadItems(Baggage $baggage): void {
        $stmt = $this->pdo->prepare("
            SELECT i.* FROM items i 
            JOIN baggage_items bi ON i.id = bi.item_id 
            WHERE bi.baggage_id = ?
        ");
        $stmt->execute([$baggage->getId()]);
        $rows = $stmt->fetchAll();
        
        $items = [];
        foreach ($rows as $row) {
            $item = $this->itemRepository->createItemFromRow($row);
            $items[] = $item;
        }
        
        $baggage->setItems($items);
    }
}