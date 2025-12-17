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
                    
                    <!-- 今日の持ち物リスト -->
                    <div class="bg-white rounded-lg shadow p-6 mb-8">
                        <?php if (empty($todaysBaggage)): ?>
                            <div class="text-gray-500 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A9.971 9.971 0 0124 24c4.21 0 7.813 2.602 9.288 6.286" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">持ち物が登録されていません</h3>
                                <p class="mt-1 text-sm text-gray-500">今日の持ち物を登録してください</p>
                            </div>
                        <?php else: ?>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">今日の持ち物一覧</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php foreach ($todaysBaggage as $item): ?>
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <?php if ($item->getImage()): ?>
                                            <img src="<?php echo htmlspecialchars($item->getImage(), ENT_QUOTES, 'UTF-8'); ?>" 
                                                 alt="<?php echo htmlspecialchars($item->getName()->getName(), ENT_QUOTES, 'UTF-8'); ?>"
                                                 class="w-full h-32 object-cover rounded-md mb-3">
                                        <?php else: ?>
                                            <div class="w-full h-32 bg-gray-200 rounded-md mb-3 flex items-center justify-center">
                                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <h4 class="font-medium text-gray-900 mb-1"><?php echo htmlspecialchars($item->getName()->getName(), ENT_QUOTES, 'UTF-8'); ?></h4>
                                        
                                        <?php if ($item->getTagId()): ?>
                                            <p class="text-sm text-gray-500 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                                <?php echo htmlspecialchars($item->getTagId()->getId(), ENT_QUOTES, 'UTF-8'); ?>
                                            </p>
                                        <?php else: ?>
                                            <p class="text-sm text-yellow-600 flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                </svg>
                                                タグ未登録
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- アクションボタン -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <a href="/baggage/register" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md font-medium text-center inline-block">
                            持ち物を登録
                        </a>
                        <a href="/template/select" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md font-medium text-center inline-block">
                            テンプレートから選択
                        </a>
                        <a href="/rfid/scan" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-md font-medium text-center inline-block">
                            RFIDスキャン
                        </a>
                        <a href="/item/register" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-md font-medium text-center inline-block">
                            アイテム管理
                        </a>
                    </div>

                    <!-- 天気情報と提案エリア -->
                    <?php if (!empty($weatherSuggestions) || $weatherSuggestion): ?>
                        <div class="mt-8 bg-blue-50 rounded-lg p-6">
                            <h3 class="text-lg font-medium text-blue-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.4 4.4 0 003 15z" />
                                </svg>
                                天気に基づく提案
                            </h3>
                            
                            <?php if ($weatherSuggestion): ?>
                                <div class="mb-4 p-4 bg-white rounded-md border border-blue-200">
                                    <p class="text-blue-800 mb-3"><?php echo htmlspecialchars($weatherSuggestion, ENT_QUOTES, 'UTF-8'); ?></p>
                                    <button 
                                        onclick="addWeatherSuggestion('傘')"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        傘を追加
                                    </button>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($weatherSuggestions)): ?>
                                <div class="space-y-3">
                                    <?php foreach ($weatherSuggestions as $suggestion): ?>
                                        <div class="flex items-center justify-between p-3 bg-white rounded-md border border-blue-200">
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($suggestion['item'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($suggestion['reason'], ENT_QUOTES, 'UTF-8'); ?></p>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <?php if ($suggestion['priority'] === 'high'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        重要
                                                    </span>
                                                <?php elseif ($suggestion['priority'] === 'medium'): ?>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        推奨
                                                    </span>
                                                <?php endif; ?>
                                                <button 
                                                    onclick="addWeatherSuggestion('<?php echo htmlspecialchars($suggestion['item'], ENT_QUOTES, 'UTF-8'); ?>')"
                                                    class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors"
                                                >
                                                    追加
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('忘れ物防止アプリが読み込まれました');
        });

        // 天気提案をアイテムに追加する関数
        function addWeatherSuggestion(itemName) {
            const formData = new FormData();
            formData.append('item_name', itemName);
            formData.append('csrf_token', '<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>');

            fetch('/weather/add-suggestion', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 成功メッセージを表示
                    showMessage(data.message, 'success');
                    // ページをリロードして更新された持ち物を表示
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('提案アイテムの追加中にエラーが発生しました', 'error');
            });
        }

        // メッセージ表示関数
        function showMessage(message, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'
            }`;
            messageDiv.textContent = message;
            
            document.body.appendChild(messageDiv);
            
            // 3秒後に自動削除
            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
        }
    </script>
</body>
</html>