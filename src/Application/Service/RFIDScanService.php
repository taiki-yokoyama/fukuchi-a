<?php

require_once __DIR__ . '/../../Domain/Repository/ItemRepository.php';
require_once __DIR__ . '/../../Domain/Entity/Item.php';

class RFIDScanService {
    private ItemRepository $itemRepository;

    public function __construct(ItemRepository $itemRepository) {
        $this->itemRepository = $itemRepository;
    }

    /**
     * RFIDタグをスキャンしてアイテムを識別する
     * 
     * @param string $tagId スキャンされたタグID
     * @return ?Item 識別されたアイテム（見つからない場合はnull）
     */
    public function scanTag(string $tagId): ?Item {
        return $this->itemRepository->findByTagId($tagId);
    }

    /**
     * スキャン結果と今日の持ち物を比較する
     * 
     * @param array $scannedItems スキャンされたアイテムの配列
     * @param array $todaysBaggage 今日の持ち物の配列
     * @return array 比較結果 ['scanned' => [...], 'missing' => [...], 'extra' => [...]]
     */
    public function compareBaggage(array $scannedItems, array $todaysBaggage): array {
        $scannedIds = array_map(fn($item) => $item->getId(), $scannedItems);
        $baggageIds = array_map(fn($item) => $item->getId(), $todaysBaggage);

        // 不足しているアイテム（今日の持ち物にあるがスキャンされていない）
        $missingIds = array_diff($baggageIds, $scannedIds);
        $missing = array_filter($todaysBaggage, fn($item) => in_array($item->getId(), $missingIds));

        // 余分なアイテム（スキャンされたが今日の持ち物にない）
        $extraIds = array_diff($scannedIds, $baggageIds);
        $extra = array_filter($scannedItems, fn($item) => in_array($item->getId(), $extraIds));

        return [
            'scanned' => $scannedItems,
            'missing' => array_values($missing),
            'extra' => array_values($extra),
            'complete' => empty($missing) // 全て揃っているかどうか
        ];
    }

    /**
     * 複数のタグIDを一括でスキャンする
     * 
     * @param array $tagIds タグIDの配列
     * @return array スキャン結果 ['found' => [...], 'not_found' => [...]]
     */
    public function batchScan(array $tagIds): array {
        $found = [];
        $notFound = [];

        foreach ($tagIds as $tagId) {
            $item = $this->scanTag($tagId);
            if ($item !== null) {
                $found[] = $item;
            } else {
                $notFound[] = $tagId;
            }
        }

        return [
            'found' => $found,
            'not_found' => $notFound
        ];
    }
}