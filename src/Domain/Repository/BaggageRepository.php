<?php

require_once __DIR__ . '/../Entity/Baggage.php';

interface BaggageRepository {
    public function findByUserAndDate(int $userId, string $date): array;
    public function findTemplatesByUser(int $userId): array;
    public function save(Baggage $baggage): void;
    public function findById(int $id): ?Baggage;
}