<?php

class Baggage {
    private ?int $id;
    private int $userId;
    private ?string $date;
    private bool $isTemplate;
    private ?string $name;
    private array $items;

    public function __construct(?int $id, int $userId, ?string $date, bool $isTemplate = false, ?string $name = null) {
        $this->id = $id;
        $this->userId = $userId;
        $this->date = $date;
        $this->isTemplate = $isTemplate;
        $this->name = $name;
        $this->items = [];
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getUserId(): int {
        return $this->userId;
    }

    public function getDate(): ?string {
        return $this->date;
    }

    public function isTemplate(): bool {
        return $this->isTemplate;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function getItems(): array {
        return $this->items;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function addItem(Item $item): void {
        $this->items[] = $item;
    }

    public function setItems(array $items): void {
        $this->items = $items;
    }
}