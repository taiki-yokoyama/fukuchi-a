<?php

class DatabaseConnection {
    private static ?PDO $pdo = null;

    public static function getInstance(): PDO {
        if (self::$pdo === null) {
            $dbPath = __DIR__ . '/../../database/database.db';
            $schemaPath = __DIR__ . '/../../database/schema.sql';
            
            // データベースディレクトリが存在しない場合は作成
            $dbDir = dirname($dbPath);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            
            // データベース接続を作成
            self::$pdo = new PDO('sqlite:' . $dbPath);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // スキーマを初期化（ファイルが存在する場合）
            if (file_exists($schemaPath)) {
                $schema = file_get_contents($schemaPath);
                self::$pdo->exec($schema);
            }
        }
        
        return self::$pdo;
    }

    // テスト用にPDOインスタンスをリセット
    public static function reset(): void {
        self::$pdo = null;
    }
}
