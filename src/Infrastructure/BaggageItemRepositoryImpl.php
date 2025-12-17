<?php

require_once __DIR__ . '/../Domain/Repository/BaggageItemRepository.php';
require_once __DIR__ . '/../Domain/Entity/BaggageItem.php';
require_once __DIR__ . '/pdo.php';

class BaggageItemRepositoryImpl implements BaggageItemRepository {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance();
    }

    public function save(BaggageItem $baggageItem): void {
        $stmt = $this->pdo->prepare("INSERT INTO baggage_items (baggage_id, item_id) VALUES (?, ?)");
        $stmt->execute([$baggageItem->getBaggageId(), $baggageItem->getItemId()]);
    }

    public function findByBaggageId(int $baggageId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM baggage_items WHERE baggage_id = ?");
        $stmt->execute([$baggageId]);
        $rows = $stmt->fetchAll();
        
        $baggageItems = [];
        foreach ($rows as $row) {
            $baggageItems[] = new BaggageItem($row['baggage_id'], $row['item_id']);
        }
        
        return $baggageItems;
    }

    public function deleteByBaggageId(int $baggageId): void {
        $stmt = $this->pdo->prepare("DELETE FROM baggage_items WHERE baggage_id = ?");
        $stmt->execute([$baggageId]);
    }
}