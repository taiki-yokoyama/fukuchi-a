<?php

require_once __DIR__ . '/../../Domain/Repository/BaggageRepository.php';
require_once __DIR__ . '/../../Domain/Repository/ItemRepository.php';
require_once __DIR__ . '/../../Domain/Entity/Baggage.php';
require_once __DIR__ . '/../../Domain/Entity/Item.php';

class BaggageRegisterUsecase {
    private BaggageRepository $baggageRepository;
    private ItemRepository $itemRepository;

    public function __construct(BaggageRepository $baggageRepository, ItemRepository $itemRepository) {
        $this->baggageRepository = $baggageRepository;
        $this->itemRepository = $itemRepository;
    }

    /**
     * 持ち物を登録する
     * 
     * @param int $userId ユーザーID
     * @param string $date 日付
     * @param array $itemIds アイテムIDの配列
     * @return void
     */
    public function registerBaggage(int $userId, string $date, array $itemIds): void {
        // 持ち物セットを作成
        $baggage = new Baggage(null, $userId, $date, false);
        
        // アイテムを取得して関連付け
        foreach ($itemIds as $itemId) {
            $item = $this->itemRepository->findById($itemId);
            if ($item !== null) {
                $baggage->addItem($item);
            }
        }
        
        // 持ち物セットを保存
        $this->baggageRepository->save($baggage);
    }

    /**
     * テンプレートを作成する
     * 
     * @param int $userId ユーザーID
     * @param string $name テンプレート名
     * @param array $itemIds アイテムIDの配列
     * @return void
     */
    public function createTemplate(int $userId, string $name, array $itemIds): void {
        // テンプレートを作成（日付なし、is_template = true）
        $template = new Baggage(null, $userId, null, true, $name);
        
        // アイテムを取得して関連付け
        foreach ($itemIds as $itemId) {
            $item = $this->itemRepository->findById($itemId);
            if ($item !== null) {
                $template->addItem($item);
            }
        }
        
        // テンプレートを保存
        $this->baggageRepository->save($template);
    }

    /**
     * テンプレートを適用して持ち物セットを作成する
     * 
     * @param int $userId ユーザーID
     * @param int $templateId テンプレートID
     * @param string $date 適用する日付
     * @return void
     */
    public function applyTemplate(int $userId, int $templateId, string $date): void {
        // テンプレートを取得
        $template = $this->baggageRepository->findById($templateId);
        
        if ($template === null || $template->getUserId() !== $userId || !$template->isTemplate()) {
            throw new InvalidArgumentException("指定されたテンプレートが見つからないか、アクセス権限がありません");
        }
        
        // テンプレートのアイテムIDを取得
        $itemIds = [];
        foreach ($template->getItems() as $item) {
            $itemIds[] = $item->getId();
        }
        
        // 新しい持ち物セットを作成
        $this->registerBaggage($userId, $date, $itemIds);
    }

    /**
     * ユーザーのテンプレート一覧を取得する
     * 
     * @param int $userId ユーザーID
     * @return array テンプレートの配列
     */
    public function getTemplatesByUser(int $userId): array {
        return $this->baggageRepository->findTemplatesByUser($userId);
    }

    /**
     * 特定の日付の持ち物セットを取得する
     * 
     * @param int $userId ユーザーID
     * @param string $date 日付
     * @return array 持ち物セットの配列
     */
    public function getBaggagesByDate(int $userId, string $date): array {
        return $this->baggageRepository->findByUserAndDate($userId, $date);
    }
}