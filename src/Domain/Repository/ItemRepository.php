<?php

require_once __DIR__ . '/../Entity/Item.php';
require_once __DIR__ . '/../ValueObject/TagId.php';

interface ItemRepository {
    public function findByUser(int $userId): array;
    public function findByTagId(string $tagId): ?Item;
    public function save(Item $item, int $userId = null): void;
    public function findById(int $id): ?Item;
}