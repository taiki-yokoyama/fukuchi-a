<?php

require_once __DIR__ . '/../Application/Usecase/ItemRegisterUsecase.php';
require_once __DIR__ . '/../Infrastructure/ItemRepositoryImpl.php';
require_once __DIR__ . '/../Infrastructure/pdo.php';

class TagController {
    private ItemRegisterUsecase $itemRegisterUsecase;

    public function __construct() {
        $pdo = getPDO();
        $itemRepository = new ItemRepositoryImpl($pdo);
        $this->itemRegisterUsecase = new ItemRegisterUsecase($itemRepository);
    }

    /**
     * タグ登録画面を表示
     */
    public function showTagRegister(): void {
        // ログインチェック
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            
            // タグが割り当てられていないアイテムを取得
            $untaggedItems = $this->itemRegisterUsecase->getUntaggedItems($userId);
            
            // 全てのアイテムを取得（管理用）
            $allItems = $this->itemRegisterUsecase->getItemsByUser($userId);
            
            require_once __DIR__ . '/../View/tagRegister.php';
        } catch (Exception $e) {
            error_log('Tag register display error: ' . $e->getMessage());
            $_SESSION['error'] = 'タグ登録画面の表示中にエラーが発生しました';
            header('Location: /');
            exit;
        }
    }

    /**
     * タグIDを割り当て
     */
    public function assignTag(): void {
        // ログインチェック
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tag/register');
            exit;
        }

        try {
            $itemId = $_POST['item_id'] ?? null;
            $tagId = $_POST['tag_id'] ?? null;

            if (!$itemId || !$tagId) {
                $_SESSION['error'] = 'アイテムIDとタグIDは必須です';
                header('Location: /tag/register');
                exit;
            }

            // タグIDを割り当て
            $this->itemRegisterUsecase->assignTagToItem((int)$itemId, $tagId);
            
            $_SESSION['success'] = 'タグIDが正常に割り当てられました';
            header('Location: /tag/register');
            exit;

        } catch (InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /tag/register');
            exit;
        } catch (Exception $e) {
            error_log('Tag assignment error: ' . $e->getMessage());
            $_SESSION['error'] = 'タグ割り当て中にエラーが発生しました';
            header('Location: /tag/register');
            exit;
        }
    }

    /**
     * タグ管理画面を表示
     */
    public function showTagManagement(): void {
        // ログインチェック
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            
            // 全てのアイテムを取得
            $allItems = $this->itemRegisterUsecase->getItemsByUser($userId);
            
            require_once __DIR__ . '/../View/tagManagement.php';
        } catch (Exception $e) {
            error_log('Tag management display error: ' . $e->getMessage());
            $_SESSION['error'] = 'タグ管理画面の表示中にエラーが発生しました';
            header('Location: /');
            exit;
        }
    }

    /**
     * タグIDを削除（アイテムからタグの関連付けを解除）
     */
    public function removeTag(): void {
        // ログインチェック
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /tag/manage');
            exit;
        }

        try {
            $itemId = $_POST['item_id'] ?? null;

            if (!$itemId) {
                $_SESSION['error'] = 'アイテムIDは必須です';
                header('Location: /tag/manage');
                exit;
            }

            // アイテムを取得
            $item = $this->itemRegisterUsecase->getItemsByUser($_SESSION['user_id']);
            $targetItem = null;
            
            foreach ($item as $itm) {
                if ($itm->getId() == $itemId) {
                    $targetItem = $itm;
                    break;
                }
            }

            if (!$targetItem) {
                $_SESSION['error'] = '指定されたアイテムが見つかりません';
                header('Location: /tag/manage');
                exit;
            }

            // タグIDをnullに設定して保存
            $targetItem->setTagId(null);
            
            // ItemRepositoryを直接使用してアイテムを更新
            $pdo = getPDO();
            $itemRepository = new ItemRepositoryImpl($pdo);
            $itemRepository->save($targetItem);
            
            $_SESSION['success'] = 'タグの関連付けが解除されました';
            header('Location: /tag/manage');
            exit;

        } catch (Exception $e) {
            error_log('Tag removal error: ' . $e->getMessage());
            $_SESSION['error'] = 'タグ削除中にエラーが発生しました';
            header('Location: /tag/manage');
            exit;
        }
    }

    /**
     * RFIDスキャン結果からタグIDを取得（AJAX用）
     */
    public function scanRfidTag(): void {
        // ログインチェック
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => '認証が必要です']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => '許可されていないメソッドです']);
            exit;
        }

        try {
            // 実際のRFIDスキャン処理はここで実装
            // 現在はモック実装として、ランダムなタグIDを生成
            $scannedTagId = 'TAG' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            
            // タグIDが既に使用されているかチェック
            $existingItem = $this->itemRegisterUsecase->findItemByTagId($scannedTagId);
            
            if ($existingItem) {
                echo json_encode([
                    'success' => false,
                    'error' => 'このタグIDは既に使用されています',
                    'tagId' => $scannedTagId
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'tagId' => $scannedTagId
                ]);
            }
            
        } catch (Exception $e) {
            error_log('RFID scan error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'スキャン中にエラーが発生しました']);
        }
    }
}