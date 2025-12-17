<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タグ登録 - 忘れ物防止アプリ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- ナビゲーション -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-semibold text-gray-900">忘れ物防止アプリ</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/tag/manage" class="text-gray-600 hover:text-gray-900">タグ管理</a>
                    <a href="/item/register" class="text-gray-600 hover:text-gray-900">アイテム登録</a>
                    <a href="/" class="text-gray-600 hover:text-gray-900">トップ</a>
                    <form method="POST" action="/logout" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="text-gray-600 hover:text-gray-900">ログアウト</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">タグ登録</h1>
            <p class="mt-2 text-gray-600">アイテムにRFIDタグIDを割り当てます</p>
        </div>

        <!-- メッセージ表示 -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800"><?= htmlspecialchars($_SESSION['success']) ?></p>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800"><?= htmlspecialchars($_SESSION['error']) ?></p>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (empty($untaggedItems)): ?>
            <!-- タグが割り当てられていないアイテムがない場合 -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">全てのアイテムにタグが割り当てられています</h3>
                <p class="mt-1 text-sm text-gray-500">タグが割り当てられていないアイテムはありません。</p>
                <div class="mt-6">
                    <a href="/item/register" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        新しいアイテムを追加
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- タグ登録フォーム -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">タグが未割り当てのアイテム</h2>
                    <p class="mt-1 text-sm text-gray-500">以下のアイテムにタグIDを割り当てることができます</p>
                </div>

                <div class="p-6">
                    <form method="POST" action="/tag/assign" id="tagAssignForm">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- アイテム選択 -->
                            <div>
                                <label for="item_id" class="block text-sm font-medium text-gray-700">アイテム選択</label>
                                <select id="item_id" name="item_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    <option value="">アイテムを選択してください</option>
                                    <?php foreach ($untaggedItems as $item): ?>
                                        <option value="<?= $item->getId() ?>">
                                            <?= htmlspecialchars($item->getName()->getName()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- タグID入力 -->
                            <div>
                                <label for="tag_id" class="block text-sm font-medium text-gray-700">タグID</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="text" 
                                           id="tag_id" 
                                           name="tag_id" 
                                           required 
                                           pattern="[A-Za-z0-9]{4,20}"
                                           title="4-20文字の英数字を入力してください"
                                           class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                                           placeholder="例: TAG1234">
                                    <button type="button" 
                                            id="scanRfidBtn"
                                            class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm hover:bg-gray-100">
                                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h-.01M12 12v.01" />
                                        </svg>
                                        スキャン
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">4-20文字の英数字を入力するか、RFIDスキャンボタンを使用してください</p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="/" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                                キャンセル
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                タグを割り当て
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- アイテムプレビュー -->
            <div class="mt-8 bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">アイテム詳細</h3>
                </div>
                <div class="p-6">
                    <div id="itemPreview" class="hidden">
                        <div class="flex items-center space-x-4">
                            <div id="itemImage" class="flex-shrink-0">
                                <!-- アイテム画像がここに表示される -->
                            </div>
                            <div>
                                <h4 id="itemName" class="text-lg font-medium text-gray-900"></h4>
                                <p class="text-sm text-gray-500">このアイテムにタグIDを割り当てます</p>
                            </div>
                        </div>
                    </div>
                    <div id="noItemSelected" class="text-center py-8 text-gray-500">
                        アイテムを選択すると詳細が表示されます
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // アイテム選択時のプレビュー表示
        document.getElementById('item_id').addEventListener('change', function() {
            const selectedItemId = this.value;
            const itemPreview = document.getElementById('itemPreview');
            const noItemSelected = document.getElementById('noItemSelected');
            
            if (selectedItemId) {
                // 選択されたアイテムの情報を表示
                const selectedOption = this.options[this.selectedIndex];
                const itemName = selectedOption.text;
                
                document.getElementById('itemName').textContent = itemName;
                
                // 画像は実装に応じて設定
                document.getElementById('itemImage').innerHTML = 
                    '<div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">' +
                    '<svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />' +
                    '</svg>' +
                    '</div>';
                
                itemPreview.classList.remove('hidden');
                noItemSelected.classList.add('hidden');
            } else {
                itemPreview.classList.add('hidden');
                noItemSelected.classList.remove('hidden');
            }
        });

        // RFIDスキャン機能
        document.getElementById('scanRfidBtn').addEventListener('click', function() {
            const button = this;
            const originalText = button.innerHTML;
            
            // ボタンを無効化してローディング表示
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>スキャン中...';
            
            // AJAX でRFIDスキャンを実行
            fetch('/tag/scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('tag_id').value = data.tagId;
                } else {
                    alert('エラー: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('スキャン中にエラーが発生しました');
            })
            .finally(() => {
                // ボタンを元に戻す
                button.disabled = false;
                button.innerHTML = originalText;
            });
        });

        // フォーム送信時のバリデーション
        document.getElementById('tagAssignForm').addEventListener('submit', function(e) {
            const itemId = document.getElementById('item_id').value;
            const tagId = document.getElementById('tag_id').value;
            
            if (!itemId) {
                e.preventDefault();
                alert('アイテムを選択してください');
                return;
            }
            
            if (!tagId) {
                e.preventDefault();
                alert('タグIDを入力してください');
                return;
            }
            
            // タグIDの形式チェック
            const tagIdPattern = /^[A-Za-z0-9]{4,20}$/;
            if (!tagIdPattern.test(tagId)) {
                e.preventDefault();
                alert('タグIDは4-20文字の英数字である必要があります');
                return;
            }
        });
    </script>
</body>
</html>