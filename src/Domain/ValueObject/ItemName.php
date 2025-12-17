<?php
class ItemName {
    private $name;

    public function __construct($name) {
        if (empty($name)) {
            throw new InvalidArgumentException("アイテム名は空にできません");
        }
        if(mb_strlen($name) > 15) {
            throw new InvalidArgumentException("アイテム名は15文字以内である必要があります");
        }
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }
}