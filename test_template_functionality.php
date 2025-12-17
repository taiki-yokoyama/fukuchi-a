<?php
/**
 * テンプレート機能のテスト
 */

// セッション開始
session_start();

// オートローダー
require_once __DIR__ . '/src/autoload.php';

// 必要なクラスの読み込み
require_once __DIR__ . '/src/Controllers/BaggageController.php';
require_once __DIR__ . '/src/Application/Usecase/BaggageRegisterUsecase.php';
require_once __DIR__ . '/src/Infrastructure/BaggageRepositoryImpl.php';
require_once __DIR__ . '/src/Infrastructure/ItemRepositoryImpl.php';
require_once __DIR__ . '/src/Infrastructure/pdo.php';

echo "=== テンプレート機能テスト ===\n\n";

try {
    // データベース接続
    $pdo = getPDO();
    echo "✓ データベース接続成功\n";
    
    // リポジトリとユースケースの初期化
    $baggageRepository = new BaggageRepositoryImpl($pdo);
    $itemRepository = new ItemRepositoryImpl($pdo);
    $baggageUsecase = new BaggageRegisterUsecase($baggageRepository, $itemRepository);
    echo "✓ リポジトリとユースケース初期化成功\n";
    
    // テストユーザーID（実際のユーザーIDを使用）
    $testUserId = 1;
    
    // 1. テンプレート作成のテスト
    echo "\n--- テンプレート作成テスト ---\n";
    
    // ユーザーのアイテムを取得
    $items = $itemRepository->findByUser($testUserId);
    if (empty($items)) {
        echo "⚠ テスト用のアイテムが見つかりません。先にアイテムを登録してください。\n";
    } else {
        echo "✓ ユーザーのアイテム数: " . count($items) . "個\n";
        
        // 最初の2つのアイテムでテンプレートを作成
        $testItemIds = array_slice(array_map(function($item) { return $item->getId(); }, $items), 0, 2);
        
        if (count($testItemIds) >= 1) {
            $templateName = "テスト用テンプレート_" . date('YmdHis');
            
            try {
                $baggageUsecase->createTemplate($testUserId, $templateName, $testItemIds);
                echo "✓ テンプレート作成成功: {$templateName}\n";
                echo "  - 含まれるアイテム数: " . count($testItemIds) . "個\n";
            } catch (Exception $e) {
                echo "✗ テンプレート作成失敗: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // 2. テンプレート一覧取得のテスト
    echo "\n--- テンプレート一覧取得テスト ---\n";
    
    try {
        $templates = $baggageUsecase->getTemplatesByUser($testUserId);
        echo "✓ テンプレート一覧取得成功\n";
        echo "  - テンプレート数: " . count($templates) . "個\n";
        
        foreach ($templates as $template) {
            echo "  - " . $template->getName() . " (アイテム数: " . count($template->getItems()) . "個)\n";
        }
        
        // 3. テンプレート適用のテスト
        if (!empty($templates)) {
            echo "\n--- テンプレート適用テスト ---\n";
            
            $testTemplate = $templates[0];
            $testDate = date('Y-m-d', strtotime('+1 day')); // 明日の日付
            
            try {
                $baggageUsecase->applyTemplate($testUserId, $testTemplate->getId(), $testDate);
                echo "✓ テンプレート適用成功\n";
                echo "  - テンプレート: " . $testTemplate->getName() . "\n";
                echo "  - 適用日: {$testDate}\n";
                
                // 適用結果を確認
                $appliedBaggages = $baggageUsecase->getBaggagesByDate($testUserId, $testDate);
                echo "  - 作成された持ち物セット数: " . count($appliedBaggages) . "個\n";
                
                if (!empty($appliedBaggages)) {
                    $appliedBaggage = $appliedBaggages[0];
                    echo "  - 含まれるアイテム数: " . count($appliedBaggage->getItems()) . "個\n";
                }
                
            } catch (Exception $e) {
                echo "✗ テンプレート適用失敗: " . $e->getMessage() . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "✗ テンプレート一覧取得失敗: " . $e->getMessage() . "\n";
    }
    
    // 4. コントローラーのテスト
    echo "\n--- コントローラーテスト ---\n";
    
    try {
        $controller = new BaggageController();
        echo "✓ BaggageController初期化成功\n";
        
        // バリデーションメソッドのテスト
        $reflection = new ReflectionClass($controller);
        
        // テンプレート作成バリデーションのテスト
        $validateTemplateCreation = $reflection->getMethod('validateTemplateCreation');
        $validateTemplateCreation->setAccessible(true);
        
        // 正常なケース
        $errors = $validateTemplateCreation->invoke($controller, "テストテンプレート", [1, 2]);
        if (empty($errors)) {
            echo "✓ テンプレート作成バリデーション（正常ケース）成功\n";
        } else {
            echo "✗ テンプレート作成バリデーション（正常ケース）失敗\n";
        }
        
        // 異常なケース
        $errors = $validateTemplateCreation->invoke($controller, "", []);
        if (!empty($errors)) {
            echo "✓ テンプレート作成バリデーション（異常ケース）成功\n";
        } else {
            echo "✗ テンプレート作成バリデーション（異常ケース）失敗\n";
        }
        
        // テンプレート適用バリデーションのテスト
        $validateTemplateApplication = $reflection->getMethod('validateTemplateApplication');
        $validateTemplateApplication->setAccessible(true);
        
        // 正常なケース
        $errors = $validateTemplateApplication->invoke($controller, 1, "2024-12-20");
        if (empty($errors)) {
            echo "✓ テンプレート適用バリデーション（正常ケース）成功\n";
        } else {
            echo "✗ テンプレート適用バリデーション（正常ケース）失敗\n";
        }
        
        // 異常なケース
        $errors = $validateTemplateApplication->invoke($controller, 0, "");
        if (!empty($errors)) {
            echo "✓ テンプレート適用バリデーション（異常ケース）成功\n";
        } else {
            echo "✗ テンプレート適用バリデーション（異常ケース）失敗\n";
        }
        
    } catch (Exception $e) {
        echo "✗ コントローラーテスト失敗: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== テスト完了 ===\n";
    
} catch (Exception $e) {
    echo "✗ テスト実行中にエラーが発生しました: " . $e->getMessage() . "\n";
    echo "スタックトレース:\n" . $e->getTraceAsString() . "\n";
}
?>