<?php

require_once __DIR__ . '/../../Domain/Repository/BaggageRepository.php';
require_once __DIR__ . '/../../Domain/Repository/ItemRepository.php';
require_once __DIR__ . '/../../Domain/Entity/Baggage.php';
require_once __DIR__ . '/../../Domain/Entity/Item.php';
require_once __DIR__ . '/../Service/WeatherService.php';

class TopUsecase {
    private BaggageRepository $baggageRepository;
    private ItemRepository $itemRepository;
    private WeatherService $weatherService;

    public function __construct(BaggageRepository $baggageRepository, ItemRepository $itemRepository, WeatherService $weatherService = null) {
        $this->baggageRepository = $baggageRepository;
        $this->itemRepository = $itemRepository;
        $this->weatherService = $weatherService ?? new WeatherService();
    }

    /**
     * 今日の持ち物を取得する
     * 
     * @param int $userId ユーザーID
     * @return array アイテムの配列
     */
    public function getTodaysBaggage(int $userId): array {
        $today = date('Y-m-d');
        $baggages = $this->baggageRepository->findByUserAndDate($userId, $today);
        
        $items = [];
        foreach ($baggages as $baggage) {
            $items = array_merge($items, $baggage->getItems());
        }
        
        return $items;
    }

    /**
     * 天気に基づく提案を取得する
     * 
     * @param string $location 位置情報
     * @return array 提案の配列
     */
    public function getWeatherSuggestion(string $location): array {
        try {
            $weatherData = $this->weatherService->getCurrentWeather($location);
            
            if ($weatherData) {
                return $this->weatherService->generateSuggestions($weatherData);
            }
            
            return [];
        } catch (Exception $e) {
            // 天気データが取得できない場合は提案なしで継続
            error_log("Weather API error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 簡単な雨の提案メッセージを取得する（後方互換性のため）
     * 
     * @param string $location 位置情報
     * @return ?string 提案メッセージ（雨の場合は傘の提案、それ以外はnull）
     */
    public function getSimpleWeatherSuggestion(string $location): ?string {
        try {
            $weatherData = $this->weatherService->getCurrentWeather($location);
            
            if ($weatherData && $this->weatherService->isRainy($weatherData)) {
                return "今日は雨の予報です。傘を持参することをお勧めします。";
            }
            
            return null;
        } catch (Exception $e) {
            // 天気データが取得できない場合は提案なしで継続
            error_log("Weather API error: " . $e->getMessage());
            return null;
        }
    }


}