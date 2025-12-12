<?php
class Item {
    private $id;
    private $name;
    private $tagId;

    public function __construct($id, $name, $tagId) {
        $this->id = $id;
        $this->name = $name;
        $this->tagId = $tagId;
    }
}