<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFIDスキャン - もってくん</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- ナビゲーションバー -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-semibold text-gray-900 hover:text-blue-600">もってくん</a>
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
        <div class="px-4 py-6 sm:px-0">
            <!-- ヘッダー -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">RFIDスキャン</h1>
                <p class="mt-2 text-gray-600">持ち物のRFIDタグをスキャンして確認しましょう</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- スキャンエリア -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">スキャン操作</h2>
                    
                    <!-- スキャン状態表示 -->
                    <div id="scan-status" class="mb-6 p-4 rounded-md bg-blue-50 border border-blue-200">
                        <div class="flex items-center">
                            <div class="animate-pulse">
                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <span class="ml-2 text-blue-800 font-medium">スキャン待機中...</span>
                        </div>
                    </div>

                    <!-- 手動入力フォーム -->
                    <form id="rfid-form" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                        
                        <div>
                            <label for="tag_id" class="block text-sm font-medium text-gray-700">タグID</label>
                            <div class="mt-1 flex rounded-md shadow-sm">
                                <input 
                                    type="text" 
                                    id="tag_id" 
                                    name="tag_id" 
                                    class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="RFIDタグIDを入力またはスキャン"
                                    required
                                >
                                <button 
                                    type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 rounded-r-md bg-blue-600 text-white hover:bg-blue-700 focus:ring-2 focus:ring-blue-500"
                                >
                                    スキャン
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- スキャン結果 -->
                    <div id="scan-result" class="mt-6 hidden">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">スキャン結果</h3>
                        <div id="scanned-item" class="border rounded-lg p-4">
                            <!-- スキャン結果がここに表示される -->
                        </div>
                    </div>
                </div>

                <!-- 今日の持ち物比較 -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">今日の持ち物</h2>
                    
                    <?php if (empty($todaysBaggage)): ?>
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A9.971 9.971 0 0124 24c4.21 0 7.813 2.602 9.288 6.286" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">持ち物が登録されていません</h3>
                            <p class="mt-1 text-sm text-gray-500">今日の持ち物を登録してください</p>
                            <div class="mt-4">
                                <a href="/baggage/register" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    持ち物を登録
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div id="baggage-items" class="space-y-3">
                            <?php foreach ($todaysBaggage as $item): ?>
                                <div class="flex items-center p-3 border rounded-lg baggage-item" data-item-id="<?php echo $item->getId(); ?>">
                                    <?php if ($item->getImage()): ?>
                                        <img src="<?php echo htmlspecialchars($item->getImage(), ENT_QUOTES, 'UTF-8'); ?>" 
                                             alt="<?php echo htmlspecialchars($item->getName()->getName(), ENT_QUOTES, 'UTF-8'); ?>"
                                             class="w-12 h-12 object-cover rounded-md mr-3">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-gray-200 rounded-md mr-3 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900"><?php echo htmlspecialchars($item->getName()->getName(), ENT_QUOTES, 'UTF-8'); ?></h4>
                                        <?php if ($item->getTagId()): ?>
                                            <p class="text-sm text-gray-500">タグID: <?php echo htmlspecialchars($item->getTagId()->getId(), ENT_QUOTES, 'UTF-8'); ?></p>
                                        <?php else: ?>
                                            <p class="text-sm text-yellow-600">タグ未登録</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="scan-status-icon">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- スキャン進捗 -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">スキャン進捗</span>
                                <span id="scan-progress-text" class="text-sm text-gray-500">0 / <?php echo count($todaysBaggage); ?></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div id="scan-progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- アクションボタン -->
            <div class="mt-8 flex justify-center space-x-4">
                <a href="/" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    トップに戻る
                </a>
                <button id="clear-scan" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md text-white bg-gray-600 hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    リセット
                </button>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('rfid-form');
            const tagInput = document.getElementById('tag_id');
            const scanResult = document.getElementById('scan-result');
            const scannedItem = document.getElementById('scanned-item');
            const scanStatus = document.getElementById('scan-status');
            const clearButton = document.getElementById('clear-scan');
            const progressBar = document.getElementById('scan-progress-bar');
            const progressText = document.getElementById('scan-progress-text');
            
            let scannedItems = new Set();
            const totalItems = <?php echo count($todaysBaggage); ?>;

            // フォーム送信処理
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const tagId = tagInput.value.trim();
                if (!tagId) {
                    showError('タグIDを入力してください');
                    return;
                }

                scanRfidTag(tagId);
            });

            // RFIDタグスキャン処理
            function scanRfidTag(tagId) {
                showScanning();
                
                const formData = new FormData();
                formData.append('tag_id', tagId);
                formData.append('csrf_token', '<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>');

                fetch('/rfid/scan', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showScanResult(data.item);
                        updateBaggageStatus(data.item.id);
                        tagInput.value = '';
                    } else {
                        showError(data.message || 'スキャンに失敗しました');
                    }
                })
                .catch(error => {
                    console.error('Scan error:', error);
                    showError('スキャン処理中にエラーが発生しました');
                });
            }

            // スキャン中表示
            function showScanning() {
                scanStatus.innerHTML = `
                    <div class="flex items-center">
                        <div class="animate-spin">
                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                        <span class="ml-2 text-blue-800 font-medium">スキャン中...</span>
                    </div>
                `;
            }

            // スキャン結果表示
            function showScanResult(item) {
                scanResult.classList.remove('hidden');
                scannedItem.innerHTML = `
                    <div class="flex items-center">
                        ${item.image ? 
                            `<img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-cover rounded-md mr-4">` :
                            `<div class="w-16 h-16 bg-gray-200 rounded-md mr-4 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>`
                        }
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">${item.name}</h4>
                            <p class="text-sm text-gray-500">タグID: ${item.tag_id}</p>
                        </div>
                        <div class="text-green-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                `;

                showSuccess('アイテムをスキャンしました');
            }

            // 持ち物ステータス更新
            function updateBaggageStatus(itemId) {
                const baggageItem = document.querySelector(`[data-item-id="${itemId}"]`);
                if (baggageItem) {
                    const statusIcon = baggageItem.querySelector('.scan-status-icon');
                    statusIcon.innerHTML = `
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    `;
                    baggageItem.classList.add('bg-green-50', 'border-green-200');
                    
                    scannedItems.add(itemId);
                    updateProgress();
                }
            }

            // 進捗更新
            function updateProgress() {
                const scannedCount = scannedItems.size;
                const percentage = totalItems > 0 ? (scannedCount / totalItems) * 100 : 0;
                
                progressBar.style.width = `${percentage}%`;
                progressText.textContent = `${scannedCount} / ${totalItems}`;
                
                if (scannedCount === totalItems && totalItems > 0) {
                    showSuccess('全てのアイテムをスキャンしました！');
                }
            }

            // エラー表示
            function showError(message) {
                scanStatus.innerHTML = `
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-2 text-red-800 font-medium">${message}</span>
                    </div>
                `;
                scanStatus.className = 'mb-6 p-4 rounded-md bg-red-50 border border-red-200';
            }

            // 成功表示
            function showSuccess(message) {
                scanStatus.innerHTML = `
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-2 text-green-800 font-medium">${message}</span>
                    </div>
                `;
                scanStatus.className = 'mb-6 p-4 rounded-md bg-green-50 border border-green-200';
                
                // 3秒後に待機状態に戻す
                setTimeout(() => {
                    scanStatus.innerHTML = `
                        <div class="flex items-center">
                            <div class="animate-pulse">
                                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <span class="ml-2 text-blue-800 font-medium">スキャン待機中...</span>
                        </div>
                    `;
                    scanStatus.className = 'mb-6 p-4 rounded-md bg-blue-50 border border-blue-200';
                }, 3000);
            }

            // リセット処理
            clearButton.addEventListener('click', function() {
                scannedItems.clear();
                updateProgress();
                
                // 全ての持ち物アイテムのスタイルをリセット
                document.querySelectorAll('.baggage-item').forEach(item => {
                    item.classList.remove('bg-green-50', 'border-green-200');
                    const statusIcon = item.querySelector('.scan-status-icon');
                    statusIcon.innerHTML = `
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    `;
                });
                
                // スキャン結果を非表示
                scanResult.classList.add('hidden');
                tagInput.value = '';
                
                showSuccess('スキャン状態をリセットしました');
            });

            // タグ入力フィールドにフォーカス
            tagInput.focus();
        });
    </script>
</body>
</html>