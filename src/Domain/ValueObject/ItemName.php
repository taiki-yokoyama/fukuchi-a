<?php
class ItemName {
    private $name;

    public function __construct($name) {
        if (empty($name)) {
            throw new InvalidArgumentException("アイテム名は空にできません");
        }
        if(strlen($name) > 50) { // Using byte length instead of character length for compatibility
            throw new InvalidArgumentException("アイテム名は50バイト以内である必要があります");
        }
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }
}