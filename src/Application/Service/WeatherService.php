<?php

require_once __DIR__ . '/../../Config/config.php';

class WeatherService {
    private string $apiKey;
    private string $apiUrl;

    public function __construct() {
        $config = Config::getConfig();
        $this->apiKey = $config['weather_api_key'];
        $this->apiUrl = $config['weather_api_url'];
    }

    /**
     * 指定された場所の現在の天気を取得する
     * 
     * @param string $location 場所（都市名など）
     * @return ?array 天気データ（取得失敗時はnull）
     */
    public function getCurrentWeather(string $location): ?array {
        if (empty($this->apiKey)) {
            error_log("Weather API key is not configured");
            return null;
        }

        $url = $this->buildApiUrl($location);
        
        try {
            $response = $this->makeApiRequest($url);
            return $this->parseWeatherResponse($response);
        } catch (Exception $e) {
            error_log("Weather API request failed: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 天気データが雨を示しているかチェックする
     * 
     * @param array $weatherData 天気データ
     * @return bool 雨かどうか
     */
    public function isRainy(array $weatherData): bool {
        if (!isset($weatherData['weather']) || !is_array($weatherData['weather'])) {
            return false;
        }

        foreach ($weatherData['weather'] as $weather) {
            if (isset($weather['main'])) {
                $condition = strtolower($weather['main']);
                if (in_array($condition, ['rain', 'drizzle', 'thunderstorm'])) {
                    return true;
                }
            }
            
            // 天気IDでもチェック（より詳細な判定）
            if (isset($weather['id'])) {
                $weatherId = $weather['id'];
                // 200-299: 雷雨, 300-399: 霧雨, 500-599: 雨
                if (($weatherId >= 200 && $weatherId < 300) ||
                    ($weatherId >= 300 && $weatherId < 400) ||
                    ($weatherId >= 500 && $weatherId < 600)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 天気に基づく持ち物提案を生成する
     * 
     * @param array $weatherData 天気データ
     * @return array 提案の配列
     */
    public function generateSuggestions(array $weatherData): array {
        $suggestions = [];

        if ($this->isRainy($weatherData)) {
            $suggestions[] = [
                'item' => '傘',
                'reason' => '雨の予報のため',
                'priority' => 'high'
            ];
        }

        // 気温に基づく提案
        if (isset($weatherData['main']['temp'])) {
            $tempCelsius = $weatherData['main']['temp'] - 273.15; // ケルビンから摂氏に変換
            
            if ($tempCelsius < 10) {
                $suggestions[] = [
                    'item' => 'コート・ジャケット',
                    'reason' => '気温が低いため',
                    'priority' => 'medium'
                ];
            } elseif ($tempCelsius > 30) {
                $suggestions[] = [
                    'item' => '日傘・帽子',
                    'reason' => '気温が高いため',
                    'priority' => 'medium'
                ];
            }
        }

        // 風速に基づく提案
        if (isset($weatherData['wind']['speed']) && $weatherData['wind']['speed'] > 10) {
            $suggestions[] = [
                'item' => '風に強い傘',
                'reason' => '強風の予報のため',
                'priority' => 'medium'
            ];
        }

        return $suggestions;
    }

    /**
     * API URLを構築する
     * 
     * @param string $location 場所
     * @return string API URL
     */
    private function buildApiUrl(string $location): string {
        $params = [
            'q' => $location,
            'appid' => $this->apiKey,
            'units' => 'metric', // 摂氏温度を使用
            'lang' => 'ja' // 日本語での天気説明
        ];

        return $this->apiUrl . '?' . http_build_query($params);
    }

    /**
     * API リクエストを実行する
     * 
     * @param string $url API URL
     * @return string レスポンス
     */
    private function makeApiRequest(string $url): string {
        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'method' => 'GET',
                'header' => [
                    'User-Agent: BaggageManagementApp/1.0'
                ]
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception("Failed to fetch weather data");
        }

        return $response;
    }

    /**
     * 天気APIのレスポンスを解析する
     * 
     * @param string $response APIレスポンス
     * @return ?array 解析された天気データ
     */
    private function parseWeatherResponse(string $response): ?array {
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON response from weather API");
        }

        if (isset($data['cod']) && $data['cod'] !== 200) {
            throw new Exception("Weather API error: " . ($data['message'] ?? 'Unknown error'));
        }

        return $data;
    }
}