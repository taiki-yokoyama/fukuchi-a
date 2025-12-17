<?php

require_once __DIR__ . '/../Entity/User.php';

interface UserRepository {
    public function findById(int $id): ?User;
    public function findByEmail(string $email): ?User;
    public function save(User $user): void;
}