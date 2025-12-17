<?php

require_once __DIR__ . '/../../Domain/Repository/ItemRepository.php';
require_once __DIR__ . '/../../Domain/Entity/Item.php';
require_once __DIR__ . '/../../Domain/ValueObject/ItemName.php';
require_once __DIR__ . '/../../Domain/ValueObject/TagId.php';

class ItemRegisterUsecase {
    private ItemRepository $itemRepository;

    public function __construct(ItemRepository $itemRepository) {
        $this->itemRepository = $itemRepository;
    }

    /**
     * アイテムを登録する
     * 
     * @param int $userId ユーザーID
     * @param string $name アイテム名
     * @param ?string $image 画像パス（オプション）
     * @return Item 作成されたアイテム
     */
    public function registerItem(int $userId, string $name, ?string $image = null): Item {
        $itemName = new ItemName($name);
        $item = new Item(null, $itemName, null, $image);
        
        $this->itemRepository->save($item, $userId);
        
        return $item;
    }

    /**
     * アイテムにタグIDを割り当てる
     * 
     * @param int $itemId アイテムID
     * @param string $tagId タグID
     * @return void
     */
    public function assignTagToItem(int $itemId, string $tagId): void {
        // 既存のタグIDが使用されていないかチェック
        $existingItem = $this->itemRepository->findByTagId($tagId);
        if ($existingItem !== null) {
            throw new InvalidArgumentException("指定されたタグIDは既に使用されています");
        }

        // アイテムを取得
        $item = $this->itemRepository->findById($itemId);
        if ($item === null) {
            throw new InvalidArgumentException("指定されたアイテムが見つかりません");
        }

        // タグIDを設定
        $tagIdObj = new TagId($tagId);
        $item->setTagId($tagIdObj);
        
        // アイテムを更新
        $this->itemRepository->save($item);
    }

    /**
     * ユーザーのアイテム一覧を取得する
     * 
     * @param int $userId ユーザーID
     * @return array アイテムの配列
     */
    public function getItemsByUser(int $userId): array {
        return $this->itemRepository->findByUser($userId);
    }

    /**
     * タグIDでアイテムを検索する
     * 
     * @param string $tagId タグID
     * @return ?Item アイテム（見つからない場合はnull）
     */
    public function findItemByTagId(string $tagId): ?Item {
        return $this->itemRepository->findByTagId($tagId);
    }

    /**
     * タグが割り当てられていないアイテムを取得する
     * 
     * @param int $userId ユーザーID
     * @return array タグが割り当てられていないアイテムの配列
     */
    public function getUntaggedItems(int $userId): array {
        $allItems = $this->itemRepository->findByUser($userId);
        $untaggedItems = [];
        
        foreach ($allItems as $item) {
            if (!$item->hasTag()) {
                $untaggedItems[] = $item;
            }
        }
        
        return $untaggedItems;
    }
}