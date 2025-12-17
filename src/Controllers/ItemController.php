<?php

require_once __DIR__ . '/../Application/Usecase/ItemRegisterUsecase.php';
require_once __DIR__ . '/../Infrastructure/ItemRepositoryImpl.php';
require_once __DIR__ . '/../Infrastructure/pdo.php';

class ItemController {
    private ItemRegisterUsecase $itemRegisterUsecase;

    public function __construct() {
        $pdo = getPDO();
        $itemRepository = new ItemRepositoryImpl($pdo);
        $this->itemRegisterUsecase = new ItemRegisterUsecase($itemRepository);
    }

    /**
     * アイテム登録画面を表示
     */
    public function showRegister(): void {
        // ログインチェック
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $items = $this->itemRegisterUsecase->getItemsByUser($userId);

        require_once __DIR__ . '/../View/itemRegister.php';
    }

    /**
     * アイテムを登録
     */
    public function register(): void {
        // ログインチェック
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /item/register');
            exit;
        }

        try {
            $userId = $_SESSION['user_id'];
            $name = $_POST['name'] ?? '';

            if (empty($name)) {
                $_SESSION['error'] = 'アイテム名は必須です';
                header('Location: /item/register');
                exit;
            }

            // 画像アップロード処理
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $uploadPath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                    $image = '/uploads/' . $filename;
                }
            }

            $this->itemRegisterUsecase->registerItem($userId, $name, $image);

            $_SESSION['success'] = 'アイテムを登録しました';
            header('Location: /item/register');
            exit;

        } catch (InvalidArgumentException $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /item/register');
            exit;
        } catch (Exception $e) {
            error_log('Item registration error: ' . $e->getMessage());
            $_SESSION['error'] = 'アイテム登録中にエラーが発生しました';
            header('Location: /item/register');
            exit;
        }
    }
}