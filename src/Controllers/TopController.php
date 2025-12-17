<?php

require_once __DIR__ . '/../Application/Usecase/TopUsecase.php';
require_once __DIR__ . '/../Infrastructure/BaggageRepositoryImpl.php';
require_once __DIR__ . '/../Infrastructure/ItemRepositoryImpl.php';
require_once __DIR__ . '/../Application/Service/WeatherService.php';

class TopController {
    private TopUsecase $topUsecase;

    public function __construct() {
        $baggageRepository = new BaggageRepositoryImpl();
        $itemRepository = new ItemRepositoryImpl();
        $weatherService = new WeatherService();
        $this->topUsecase = new TopUsecase($baggageRepository, $itemRepository, $weatherService);
    }

    /**
     * トップページを表示
     */
    public function showTop(): void {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $user = $this->getCurrentUser();
        $userId = $user->getId();
        
        // 今日の持ち物を取得
        $todaysBaggage = $this->topUsecase->getTodaysBaggage($userId);
        
        // 天気提案を取得（デフォルトで東京の天気を使用）
        $weatherSuggestion = $this->topUsecase->getSimpleWeatherSuggestion('Tokyo');
        $weatherSuggestions = $this->topUsecase->getWeatherSuggestion('Tokyo');
        
        // CSRFトークンを生成
        $csrfToken = $this->getCsrfToken();
        
        include __DIR__ . '/../View/top.php';
    }

    /**
     * RFIDスキャン画面を表示
     */
    public function showRfidScan(): void {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        $user = $this->getCurrentUser();
        $userId = $user->getId();
        
        // 今日の持ち物を取得（比較用）
        $todaysBaggage = $this->topUsecase->getTodaysBaggage($userId);
        
        // CSRFトークンを生成
        $csrfToken = $this->getCsrfToken();
        
        include __DIR__ . '/../View/rfid_scan.php';
    }

    /**
     * RFIDスキャン処理
     */
    public function processRfidScan(): void {
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'ログインが必要です']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => '許可されていないメソッドです']);
            return;
        }

        // CSRFトークンの検証
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRFトークンが無効です']);
            return;
        }

        $tagId = $_POST['tag_id'] ?? '';
        if (empty($tagId)) {
            http_response_code(400);
            echo json_encode(['error' => 'タグIDが指定されていません']);
            return;
        }

        try {
            $itemRepository = new ItemRepositoryImpl();
            $item = $itemRepository->findByTagId($tagId);
            
            if ($item === null) {
                echo json_encode([
                    'success' => false,
                    'message' => 'このタグIDに関連付けられたアイテムが見つかりません'
                ]);
                return;
            }

            echo json_encode([
                'success' => true,
                'item' => [
                    'id' => $item->getId(),
                    'name' => $item->getName()->getName(),
                    'image' => $item->getImage(),
                    'tag_id' => $item->getTagId()->getId()
                ]
            ]);
        } catch (Exception $e) {
            error_log('RFID scan error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'スキャン処理中にエラーが発生しました']);
        }
    }

    /**
     * 天気提案をアイテムに追加
     */
    public function addWeatherSuggestion(): void {
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'ログインが必要です']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => '許可されていないメソッドです']);
            return;
        }

        // CSRFトークンの検証
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'CSRFトークンが無効です']);
            return;
        }

        $itemName = $_POST['item_name'] ?? '';
        if (empty($itemName)) {
            http_response_code(400);
            echo json_encode(['error' => 'アイテム名が指定されていません']);
            return;
        }

        try {
            $user = $this->getCurrentUser();
            $userId = $user->getId();
            
            // 今日の日付で持ち物セットを作成または取得
            $today = date('Y-m-d');
            $baggageRepository = new BaggageRepositoryImpl();
            $itemRepository = new ItemRepositoryImpl();
            
            // 既存の今日の持ち物セットを取得
            $existingBaggages = $baggageRepository->findByUserAndDate($userId, $today);
            
            if (empty($existingBaggages)) {
                // 新しい持ち物セットを作成
                require_once __DIR__ . '/../Domain/Entity/Baggage.php';
                $baggage = new Baggage(null, $userId, $today, false, '今日の持ち物');
            } else {
                $baggage = $existingBaggages[0]; // 最初の持ち物セットを使用
            }
            
            // 提案されたアイテムがユーザーのアイテムに存在するかチェック
            $userItems = $itemRepository->findByUser($userId);
            $suggestedItem = null;
            
            foreach ($userItems as $item) {
                if (strtolower($item->getName()->getName()) === strtolower($itemName)) {
                    $suggestedItem = $item;
                    break;
                }
            }
            
            if ($suggestedItem === null) {
                echo json_encode([
                    'success' => false,
                    'message' => "「{$itemName}」がアイテムリストに見つかりません。先にアイテムを登録してください。"
                ]);
                return;
            }
            
            // アイテムが既に持ち物セットに含まれているかチェック
            $existingItems = $baggage->getItems();
            foreach ($existingItems as $existingItem) {
                if ($existingItem->getId() === $suggestedItem->getId()) {
                    echo json_encode([
                        'success' => false,
                        'message' => "「{$itemName}」は既に今日の持ち物に含まれています。"
                    ]);
                    return;
                }
            }
            
            // アイテムを持ち物セットに追加
            $baggage->addItem($suggestedItem);
            $baggageRepository->save($baggage);
            
            echo json_encode([
                'success' => true,
                'message' => "「{$itemName}」を今日の持ち物に追加しました。"
            ]);
            
        } catch (Exception $e) {
            error_log('Weather suggestion add error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => '提案アイテムの追加中にエラーが発生しました']);
        }
    }

    /**
     * ログイン状態をチェック
     */
    private function isLoggedIn(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * 現在のユーザーを取得
     */
    private function getCurrentUser(): ?object {
        if (!$this->isLoggedIn()) {
            return null;
        }

        require_once __DIR__ . '/../Infrastructure/UserRepositoryImpl.php';
        $userRepository = new UserRepositoryImpl();
        return $userRepository->findById($_SESSION['user_id']);
    }

    /**
     * CSRFトークンを生成
     */
    private function getCsrfToken(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * CSRFトークンを検証
     */
    private function validateCsrfToken(string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}