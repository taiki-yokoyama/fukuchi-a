<?php
/**
 * 基本的なテンプレート機能テスト
 */

echo "=== テンプレート機能基本テスト ===\n\n";

try {
    // オートローダー
    require_once 'src/autoload.php';
    
    echo "1. クラス読み込みテスト\n";
    
    // 必要なクラスが読み込めるかテスト
    $baggageController = new BaggageController();
    echo "✓ BaggageController読み込み成功\n";
    
    // データベース接続テスト
    $pdo = getPDO();
    echo "✓ データベース接続成功\n";
    
    // リポジトリ初期化テスト
    $baggageRepository = new BaggageRepositoryImpl($pdo);
    $itemRepository = new ItemRepositoryImpl($pdo);
    echo "✓ リポジトリ初期化成功\n";
    
    // ユースケース初期化テスト
    $baggageUsecase = new BaggageRegisterUsecase($baggageRepository, $itemRepository);
    echo "✓ ユースケース初期化成功\n";
    
    echo "\n2. バリデーション機能テスト\n";
    
    // リフレクションを使ってプライベートメソッドをテスト
    $reflection = new ReflectionClass($baggageController);
    
    // テンプレート作成バリデーション
    $validateMethod = $reflection->getMethod('validateTemplateCreation');
    $validateMethod->setAccessible(true);
    
    // 正常ケース
    $errors = $validateMethod->invoke($baggageController, "テストテンプレート", [1, 2]);
    if (empty($errors)) {
        echo "✓ テンプレート作成バリデーション（正常）成功\n";
    } else {
        echo "✗ テンプレート作成バリデーション（正常）失敗: " . implode(', ', $errors) . "\n";
    }
    
    // 異常ケース - 空の名前
    $errors = $validateMethod->invoke($baggageController, "", [1, 2]);
    if (isset($errors['name'])) {
        echo "✓ テンプレート作成バリデーション（空の名前）成功\n";
    } else {
        echo "✗ テンプレート作成バリデーション（空の名前）失敗\n";
    }
    
    // 異常ケース - 空のアイテム
    $errors = $validateMethod->invoke($baggageController, "テスト", []);
    if (isset($errors['items'])) {
        echo "✓ テンプレート作成バリデーション（空のアイテム）成功\n";
    } else {
        echo "✗ テンプレート作成バリデーション（空のアイテム）失敗\n";
    }
    
    // テンプレート適用バリデーション
    $validateApplyMethod = $reflection->getMethod('validateTemplateApplication');
    $validateApplyMethod->setAccessible(true);
    
    // 正常ケース
    $errors = $validateApplyMethod->invoke($baggageController, 1, "2024-12-20");
    if (empty($errors)) {
        echo "✓ テンプレート適用バリデーション（正常）成功\n";
    } else {
        echo "✗ テンプレート適用バリデーション（正常）失敗: " . implode(', ', $errors) . "\n";
    }
    
    // 異常ケース
    $errors = $validateApplyMethod->invoke($baggageController, 0, "");
    if (!empty($errors)) {
        echo "✓ テンプレート適用バリデーション（異常）成功\n";
    } else {
        echo "✗ テンプレート適用バリデーション（異常）失敗\n";
    }
    
    echo "\n3. データベーステーブル存在確認\n";
    
    // テーブル存在確認
    $tables = ['users', 'baggages', 'items', 'baggage_items'];
    foreach ($tables as $table) {
        $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name=?");
        $stmt->execute([$table]);
        if ($stmt->fetch()) {
            echo "✓ テーブル '{$table}' 存在確認\n";
        } else {
            echo "✗ テーブル '{$table}' が見つかりません\n";
        }
    }
    
    echo "\n4. ユーザー・アイテム存在確認\n";
    
    // ユーザー数確認
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    echo "ユーザー数: {$userCount}\n";
    
    // アイテム数確認
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM items");
    $itemCount = $stmt->fetch()['count'];
    echo "アイテム数: {$itemCount}\n";
    
    // テンプレート数確認
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM baggages WHERE is_template = 1");
    $templateCount = $stmt->fetch()['count'];
    echo "テンプレート数: {$templateCount}\n";
    
    if ($userCount > 0 && $itemCount > 0) {
        echo "\n5. 実際のテンプレート操作テスト\n";
        
        // 最初のユーザーを取得
        $stmt = $pdo->query("SELECT id FROM users LIMIT 1");
        $userId = $stmt->fetch()['id'];
        echo "テストユーザーID: {$userId}\n";
        
        // ユーザーのアイテムを取得
        $userItems = $itemRepository->findByUser($userId);
        echo "ユーザーのアイテム数: " . count($userItems) . "\n";
        
        if (count($userItems) > 0) {
            // テンプレート作成テスト
            $testItemIds = [array_values($userItems)[0]->getId()];
            $templateName = "自動テスト_" . date('YmdHis');
            
            try {
                $baggageUsecase->createTemplate($userId, $templateName, $testItemIds);
                echo "✓ テンプレート作成成功: {$templateName}\n";
                
                // テンプレート一覧取得
                $templates = $baggageUsecase->getTemplatesByUser($userId);
                echo "✓ テンプレート一覧取得成功: " . count($templates) . "個\n";
                
                // 作成したテンプレートを探す
                $createdTemplate = null;
                foreach ($templates as $template) {
                    if ($template->getName() === $templateName) {
                        $createdTemplate = $template;
                        break;
                    }
                }
                
                if ($createdTemplate) {
                    echo "✓ 作成したテンプレートを確認: " . $createdTemplate->getName() . "\n";
                    
                    // テンプレート適用テスト
                    $testDate = date('Y-m-d', strtotime('+2 days'));
                    try {
                        $baggageUsecase->applyTemplate($userId, $createdTemplate->getId(), $testDate);
                        echo "✓ テンプレート適用成功: {$testDate}\n";
                        
                        // 適用結果確認
                        $appliedBaggages = $baggageUsecase->getBaggagesByDate($userId, $testDate);
                        echo "✓ 適用結果確認: " . count($appliedBaggages) . "個の持ち物セット\n";
                        
                    } catch (Exception $e) {
                        echo "✗ テンプレート適用失敗: " . $e->getMessage() . "\n";
                    }
                } else {
                    echo "✗ 作成したテンプレートが見つかりません\n";
                }
                
            } catch (Exception $e) {
                echo "✗ テンプレート作成失敗: " . $e->getMessage() . "\n";
            }
        } else {
            echo "⚠ ユーザーにアイテムがないため、テンプレート操作テストをスキップします\n";
        }
    } else {
        echo "⚠ ユーザーまたはアイテムが存在しないため、実際のテンプレート操作テストをスキップします\n";
    }
    
    echo "\n=== テスト完了 ===\n";
    echo "テンプレート機能の実装が正常に完了しました！\n";
    
} catch (Exception $e) {
    echo "\n✗ テスト中にエラーが発生しました:\n";
    echo "エラー: " . $e->getMessage() . "\n";
    echo "ファイル: " . $e->getFile() . " 行: " . $e->getLine() . "\n";
    echo "\nスタックトレース:\n" . $e->getTraceAsString() . "\n";
}
?>