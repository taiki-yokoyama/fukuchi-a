<?php

class User {
    private ?int $id;
    private string $name;
    private string $email;
    private string $password;

    public function __construct(?int $id, string $name, string $email, string $password) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password);
    }

    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}