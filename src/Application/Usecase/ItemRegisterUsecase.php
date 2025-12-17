<?php
require_once __DIR__ . '/../../Domain/Entity/Item.php';
require_once __DIR__ . '/../../Domain/ValueObject/TagId.php';
class ItemRegisterUsecase {
    private $itemRepository;

    public function __construct($itemRepository) {
        $this->itemRepository = $itemRepository;
    }

    public function registerItem($name, $tagId) {
        // アイテム登録ロジック
        $tagid = new TagId($tagId);
        $itemName = new ItemName($name);
        $item = new Item(null, $itemName, $tagid);
        $this->itemRepository->save($item);
    }
    
}