<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>トップページ - 忘れ物防止アプリ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- ナビゲーションバー -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-900">忘れ物防止アプリ</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">
                        こんにちは、<?php echo htmlspecialchars($user->getName(), ENT_QUOTES, 'UTF-8'); ?>さん
                    </span>
                    <form method="POST" action="/logout" class="inline">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        <button 
                            type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium"
                            onclick="return confirm('ログアウトしますか？')"
                        >
                            ログアウト
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- メインコンテンツ -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- 成功メッセージ表示 -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            <?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div class="px-4 py-6 sm:px-0">
            <div class="border-4 border-dashed border-gray-200 rounded-lg p-8">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">
                        今日の持ち物
                    </h2>
                    <p class="text-gray-600 mb-8">
                        今日（<?php echo date('Y年m月d日'); ?>）の持ち物を確認しましょう
                    </p>
                    
                    <!-- 今日の持ち物リスト（現在は空の状態） -->
                    <div class="bg-white rounded-lg shadow p-6 mb-8">
                        <div class="text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A9.971 9.971 0 0124 24c4.21 0 7.813 2.602 9.288 6.286" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">持ち物が登録されていません</h3>
                            <p class="mt-1 text-sm text-gray-500">今日の持ち物を登録してください</p>
                        </div>
                    </div>

                    <!-- アクションボタン -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium">
                            持ち物を登録
                        </button>
                        <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md font-medium">
                            テンプレートから選択
                        </button>
                        <button class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-md font-medium">
                            RFIDスキャン
                        </button>
                        <button class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-md font-medium">
                            アイテム管理
                        </button>
                    </div>

                    <!-- 天気情報エリア（プレースホルダー） -->
                    <div class="mt-8 bg-blue-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-blue-900 mb-2">今日の天気</h3>
                        <p class="text-blue-700">天気情報を取得中...</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // 将来的な機能のためのプレースホルダー
        document.addEventListener('DOMContentLoaded', function() {
            console.log('忘れ物防止アプリが読み込まれました');
        });
    </script>
</body>
</html>