<?php

class Config {
    // データベース設定
    public const DB_PATH = __DIR__ . '/../../database/database.db';
    public const SCHEMA_PATH = __DIR__ . '/../../database/schema.sql';
    
    // アプリケーション設定
    public const APP_NAME = 'もってくん';
    public const SESSION_NAME = 'baggage_app_session';
    
    // セキュリティ設定
    public const PASSWORD_MIN_LENGTH = 8;
    public const SESSION_TIMEOUT = 3600; // 1時間
    
    // ファイルアップロード設定
    public const UPLOAD_DIR = __DIR__ . '/../../uploads/';
    public const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB
    public const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif'];
    
    // 天気API設定（実装時に設定）
    public const WEATHER_API_KEY = '';
    public const WEATHER_API_URL = 'https://api.openweathermap.org/data/2.5/weather';
    
    // バリデーション設定
    public const ITEM_NAME_MAX_LENGTH = 15;
    public const TAG_ID_MIN_LENGTH = 4;
    public const TAG_ID_MAX_LENGTH = 20;
    
    public static function getUploadDir(): string {
        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }
        return self::UPLOAD_DIR;
    }

    /**
     * 設定値を配列として返す（TopUsecaseで使用）
     */
    public static function getConfig(): array {
        return [
            'weather_api_key' => self::WEATHER_API_KEY,
            'weather_api_url' => self::WEATHER_API_URL,
            'db_path' => self::DB_PATH,
            'upload_dir' => self::getUploadDir(),
        ];
    }
}