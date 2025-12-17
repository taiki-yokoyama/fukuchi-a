<?php

require_once __DIR__ . '/../src/Infrastructure/pdo.php';

try {
    $pdo = DatabaseConnection::getInstance();
    echo "データベースが正常に初期化されました。\n";
    
    // テーブルが作成されたかチェック
    $tables = ['users', 'baggages', 'items', 'baggage_items'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
        if ($stmt->fetch()) {
            echo "テーブル '$table' が作成されました。\n";
        } else {
            echo "エラー: テーブル '$table' が作成されませんでした。\n";
        }
    }
} catch (Exception $e) {
    echo "データベース初期化エラー: " . $e->getMessage() . "\n";
}