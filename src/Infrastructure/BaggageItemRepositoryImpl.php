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
        try {
            $stmt = $this->pdo->prepare("INSERT INTO baggage_items (baggage_id, item_id) VALUES (?, ?)");
            $stmt->execute([$baggageItem->getBaggageId(), $baggageItem->getItemId()]);
        } catch (PDOException $e) {
            // 主キー制約違反の場合（重複挿入）
            if ($e->getCode() == 23000 || strpos($e->getMessage(), 'PRIMARY KEY constraint failed') !== false) {
                throw new RuntimeException("この持ち物セットには既に同じアイテムが含まれています", 0, $e);
            }
            
            // 外部キー制約違反の場合
            if (strpos($e->getMessage(), 'FOREIGN KEY constraint failed') !== false) {
                throw new RuntimeException("指定された持ち物セットまたはアイテムが存在しません", 0, $e);
            }
            
            throw new RuntimeException("持ち物アイテム関連付け保存中にデータベースエラーが発生しました: " . $e->getMessage(), 0, $e);
        }
    }

    public function findByBaggageId(int $baggageId): array {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM baggage_items WHERE baggage_id = ?");
            $stmt->execute([$baggageId]);
            $rows = $stmt->fetchAll();
            
            $baggageItems = [];
            foreach ($rows as $row) {
                $baggageItems[] = new BaggageItem($row['baggage_id'], $row['item_id']);
            }
            
            return $baggageItems;
        } catch (PDOException $e) {
            throw new RuntimeException("持ち物アイテム関連付け検索中にデータベースエラーが発生しました: " . $e->getMessage(), 0, $e);
        }
    }

    public function deleteByBaggageId(int $baggageId): void {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM baggage_items WHERE baggage_id = ?");
            $stmt->execute([$baggageId]);
        } catch (PDOException $e) {
            throw new RuntimeException("持ち物アイテム関連付け削除中にデータベースエラーが発生しました: " . $e->getMessage(), 0, $e);
        }
    }
}