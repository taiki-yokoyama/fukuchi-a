<?php

class TagId {
    private string $id;

    public function __construct(string $id) {
        if (empty($id)) {
            throw new InvalidArgumentException("タグIDは空にできません");
        }
        
        // RFIDタグIDの基本的な形式チェック（英数字のみ、適切な長さ）
        if (!preg_match('/^[A-Za-z0-9]{4,20}$/', $id)) {
            throw new InvalidArgumentException("タグIDは4-20文字の英数字である必要があります");
        }
        
        $this->id = $id;
    }

    public function getId(): string {
        return $this->id;
    }

    public function equals(TagId $other): bool {
        return $this->id === $other->id;
    }

    public function __toString(): string {
        return $this->id;
    }
}