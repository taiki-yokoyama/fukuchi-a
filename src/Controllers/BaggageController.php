<?php

require_once __DIR__ . '/../Application/Usecase/BaggageRegisterUsecase.php';
require_once __DIR__ . '/../Infrastructure/BaggageRepositoryImpl.php';
require_once __DIR__ . '/../Infrastructure/ItemRepositoryImpl.php';
require_once __DIR__ . '/../Infrastructure/pdo.php';

class BaggageController {
    private BaggageRegisterUsecase $baggageRegisterUsecase;

    public function __construct() {
        $pdo = getPDO();
        $baggageRepository = new BaggageRepositoryImpl($pdo);
        $itemRepository = new ItemRepositoryImpl($pdo);
        $this->baggageRegisterUsecase = new BaggageRegisterUsecase($baggageRepository, $itemRepository);
    }

    /**
     * 持ち物登録画面を表示
     */
    public function showRegister(): void {
        // ログインチェック
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        try {
            // ユーザーのアイテム一覧を取得
            $pdo = getPDO();
            $itemRepository = new ItemRepositoryImpl($pdo);
            $items = $itemRepository->findByUser($userId);
            
            // CSRFトークン生成
            $csrfToken = $this->generateCsrfToken();
            
            // 今日の日付をデフォルトに設定
            $defaultDate = date('Y-m-d');
            
            require_once __DIR__ . '/../View/baggageRegister.php';
            
        } catch (Exception $e) {
            error_log('Baggage register view error: ' . $e->getMessage());
            $_SESSION['error'] = 'アイテム一覧の取得中にエラーが発生しました';
            header('Location: /');
            exit;
        }
    }

    /**
     * 持ち物登録処理
     */
    public function register(): void {
        // ログインチェック
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // CSRFトークンチェック
        if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = '不正なリクエストです';
            header('Location: /baggage/register');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $date = $_POST['date'] ?? '';
        $itemIds = $_POST['item_ids'] ?? [];

        // バリデーション
        $errors = $this->validateBaggageRegistration($date, $itemIds);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /baggage/register');
            exit;
        }

        try {
            // 持ち物を登録
            $this->baggageRegisterUsecase->registerBaggage($userId, $date, $itemIds);
            
            $_SESSION['success'] = '持ち物を登録しました';
            header('Location: /');
            exit;
            
        } catch (Exception $e) {
            error_log('Baggage registration error: ' . $e->getMessage());
            $_SESSION['error'] = '持ち物の登録中にエラーが発生しました';
            header('Location: /baggage/register');
            exit;
        }
    }

    /**
     * 持ち物登録のバリデーション
     */
    private function validateBaggageRegistration(string $date, array $itemIds): array {
        $errors = [];

        // 日付のバリデーション
        if (empty($date)) {
            $errors['date'] = '日付を選択してください';
        } elseif (!$this->isValidDate($date)) {
            $errors['date'] = '有効な日付を入力してください';
        }

        // アイテムのバリデーション
        if (empty($itemIds)) {
            $errors['items'] = '少なくとも1つのアイテムを選択してください';
        } else {
            // アイテムIDが数値かチェック
            foreach ($itemIds as $itemId) {
                if (!is_numeric($itemId) || $itemId <= 0) {
                    $errors['items'] = '無効なアイテムが選択されています';
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * 日付の妥当性をチェック
     */
    private function isValidDate(string $date): bool {
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        return $dateTime && $dateTime->format('Y-m-d') === $date;
    }

    /**
     * CSRFトークンを生成
     */
    private function generateCsrfToken(): string {
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