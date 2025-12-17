<?php

require_once __DIR__ . '/../Entity/BaggageItem.php';

interface BaggageItemRepository {
    public function save(BaggageItem $baggageItem): void;
    public function findByBaggageId(int $baggageId): array;
    public function deleteByBaggageId(int $baggageId): void;
}