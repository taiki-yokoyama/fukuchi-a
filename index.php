<?php
// セッション設定とセキュリティ
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // HTTPSを使用する場合は1に設定
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// セッション開始
session_start();

// オートローダー
require_once __DIR__ . '/src/autoload.php';

// コントローラーの読み込み
require_once __DIR__ . '/src/Controllers/UserController.php';
require_once __DIR__ . '/src/Controllers/TopController.php';
require_once __DIR__ . '/src/Controllers/BaggageController.php';

// エラーハンドリング設定
error_reporting(E_ALL);
ini_set('display_errors', 0); // 本番環境では0に設定

// セキュリティヘッダーの設定
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// ルーティング
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// クエリパラメータを除去
$path = parse_url($requestUri, PHP_URL_PATH);

// コントローラーのインスタンス化
$userController = new UserController();
$topController = new TopController();
$baggageController = new BaggageController();

try {
    switch ($path) {
        case '/':
            // トップページ
            $topController->showTop();
            break;

        case '/login':
            if ($requestMethod === 'GET') {
                $userController->showLogin();
            } elseif ($requestMethod === 'POST') {
                $userController->login();
            }
            break;

        case '/register':
            if ($requestMethod === 'GET') {
                $userController->showRegister();
            } elseif ($requestMethod === 'POST') {
                $userController->register();
            }
            break;

        case '/logout':
            if ($requestMethod === 'POST') {
                $userController->logout();
            } else {
                header('Location: /');
                exit;
            }
            break;

        case '/rfid/scan':
            if ($requestMethod === 'GET') {
                $topController->showRfidScan();
            } elseif ($requestMethod === 'POST') {
                $topController->processRfidScan();
            }
            break;

        case '/weather/add-suggestion':
            if ($requestMethod === 'POST') {
                $topController->addWeatherSuggestion();
            } else {
                http_response_code(405);
                echo json_encode(['error' => '許可されていないメソッドです']);
            }
            break;

        case '/baggage/register':
            if ($requestMethod === 'GET') {
                $baggageController->showRegister();
            } elseif ($requestMethod === 'POST') {
                $baggageController->register();
            }
            break;

        case '/template/create':
            if ($requestMethod === 'GET') {
                $baggageController->showCreateTemplate();
            } elseif ($requestMethod === 'POST') {
                $baggageController->createTemplate();
            }
            break;

        case '/template/list':
            if ($requestMethod === 'GET') {
                $baggageController->showTemplateList();
            }
            break;

        case '/template/select':
            if ($requestMethod === 'GET') {
                $baggageController->showSelectTemplate();
            }
            break;

        case '/template/apply':
            if ($requestMethod === 'POST') {
                $baggageController->applyTemplate();
            } else {
                header('Location: /template/select');
                exit;
            }
            break;

        default:
            // 404エラー
            http_response_code(404);
            show404Page();
            break;
    }
} catch (Exception $e) {
    // エラーログに記録
    error_log('Application Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    
    // ユーザーにはシンプルなエラーメッセージを表示
    http_response_code(500);
    showErrorPage();
}



/**
 * 404エラーページを表示
 */
function show404Page(): void {
    ?>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ページが見つかりません - 忘れ物防止アプリ</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full text-center">
            <h1 class="text-6xl font-bold text-gray-900">404</h1>
            <p class="mt-4 text-xl text-gray-600">ページが見つかりません</p>
            <p class="mt-2 text-gray-500">お探しのページは存在しないか、移動された可能性があります。</p>
            <div class="mt-6">
                <a href="/" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    トップページに戻る
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
}

/**
 * エラーページを表示
 */
function showErrorPage(): void {
    ?>
    <!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>エラーが発生しました - 忘れ物防止アプリ</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full text-center">
            <h1 class="text-6xl font-bold text-gray-900">500</h1>
            <p class="mt-4 text-xl text-gray-600">サーバーエラーが発生しました</p>
            <p class="mt-2 text-gray-500">申し訳ございませんが、一時的な問題が発生しています。しばらく時間をおいてから再度お試しください。</p>
            <div class="mt-6">
                <a href="/" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    トップページに戻る
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
?>