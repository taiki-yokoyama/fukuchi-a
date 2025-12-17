<?php

require_once __DIR__ . '/../ValueObject/ItemName.php';
require_once __DIR__ . '/../ValueObject/TagId.php';

class Item {
    private ?int $id;
    private ItemName $name;
    private ?TagId $tagId;
    private ?string $image;

    public function __construct(?int $id, ItemName $name, ?TagId $tagId = null, ?string $image = null) {
        $this->id = $id;
        $this->name = $name;
        $this->tagId = $tagId;
        $this->image = $image;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ItemName {
        return $this->name;
    }

    public function getTagId(): ?TagId {
        return $this->tagId;
    }

    public function getImage(): ?string {
        return $this->image;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setTagId(?TagId $tagId): void {
        $this->tagId = $tagId;
    }

    public function setImage(string $image): void {
        $this->image = $image;
    }

    public function hasTag(): bool {
        return $this->tagId !== null;
    }
}