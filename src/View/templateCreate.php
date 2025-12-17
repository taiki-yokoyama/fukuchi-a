<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>テンプレート作成 - もってくん</title>
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
                    <a href="/" class="text-gray-700 hover:text-blue-600">トップページ</a>
                    <a href="/template/list" class="text-gray-700 hover:text-blue-600">テンプレート一覧</a>
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
    <main class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- ページタイトル -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">テンプレート作成</h1>
                <p class="mt-2 text-gray-600">再利用可能な持ち物セットのテンプレートを作成します</p>
            </div>

            <!-- エラーメッセージ表示 -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- 作成フォーム -->
            <div class="bg-white shadow rounded-lg">
                <form method="POST" action="/template/create" class="p-6 space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    
                    <!-- テンプレート名入力 -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            テンプレート名 <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="<?php echo htmlspecialchars($_SESSION['old_input']['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            placeholder="例: 出張用、ジム用、日帰り旅行用"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?php echo isset($_SESSION['errors']['name']) ? 'border-red-300' : ''; ?>"
                            required
                            maxlength="100"
                        >
                        <?php if (isset($_SESSION['errors']['name'])): ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- アイテム選択 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-4">
                            テンプレートに含めるアイテムを選択 <span class="text-red-500">*</span>
                        </label>
                        
                        <?php if (empty($items)): ?>
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A9.971 9.971 0 0124 24c4.21 0 7.813 2.602 9.288 6.286" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">アイテムが登録されていません</h3>
                                <p class="mt-1 text-sm text-gray-500">まずはアイテムを登録してください</p>
                                <div class="mt-6">
                                    <a href="/item/register" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        アイテムを登録
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <?php 
                                $selectedItems = $_SESSION['old_input']['item_ids'] ?? [];
                                foreach ($items as $item): 
                                ?>
                                    <div class="relative">
                                        <label class="flex items-start p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                            <input 
                                                type="checkbox" 
                                                name="item_ids[]" 
                                                value="<?php echo $item->getId(); ?>"
                                                class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                <?php echo in_array($item->getId(), $selectedItems) ? 'checked' : ''; ?>
                                            >
                                            <div class="ml-3 flex-1">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($item->getName()->getName(), ENT_QUOTES, 'UTF-8'); ?>
                                                    </h4>
                                                    <?php if ($item->hasTag()): ?>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                            </svg>
                                                            タグ有
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                            </svg>
                                                            タグ無
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <?php if ($item->getImage()): ?>
                                                    <img src="<?php echo htmlspecialchars($item->getImage(), ENT_QUOTES, 'UTF-8'); ?>" 
                                                         alt="<?php echo htmlspecialchars($item->getName()->getName(), ENT_QUOTES, 'UTF-8'); ?>"
                                                         class="mt-2 w-full h-24 object-cover rounded-md">
                                                <?php else: ?>
                                                    <div class="mt-2 w-full h-24 bg-gray-200 rounded-md flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if ($item->hasTag()): ?>
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        タグID: <?php echo htmlspecialchars($item->getTagId()->getId(), ENT_QUOTES, 'UTF-8'); ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <?php if (isset($_SESSION['errors']['items'])): ?>
                                <p class="mt-2 text-sm text-red-600"><?php echo htmlspecialchars($_SESSION['errors']['items'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                            
                            <!-- 選択状況表示 -->
                            <div class="mt-4 p-3 bg-blue-50 rounded-md">
                                <p class="text-sm text-blue-800">
                                    <span id="selected-count">0</span>個のアイテムが選択されています
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ボタン -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="/template/list" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            戻る
                        </a>
                        
                        <?php if (!empty($items)): ?>
                            <button 
                                type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                id="submit-button"
                                disabled
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                テンプレートを作成
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="item_ids[]"]');
            const selectedCountElement = document.getElementById('selected-count');
            const submitButton = document.getElementById('submit-button');
            const nameInput = document.getElementById('name');
            
            function updateSelectedCount() {
                const selectedCount = document.querySelectorAll('input[name="item_ids[]"]:checked').length;
                if (selectedCountElement) {
                    selectedCountElement.textContent = selectedCount;
                }
                
                updateSubmitButton();
            }
            
            function updateSubmitButton() {
                if (submitButton) {
                    const selectedCount = document.querySelectorAll('input[name="item_ids[]"]:checked').length;
                    const hasName = nameInput && nameInput.value.trim().length > 0;
                    submitButton.disabled = selectedCount === 0 || !hasName;
                }
            }
            
            // 初期状態の更新
            updateSelectedCount();
            
            // チェックボックスの変更を監視
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });
            
            // テンプレート名の変更を監視
            if (nameInput) {
                nameInput.addEventListener('input', updateSubmitButton);
            }
            
            // フォーム送信時の確認
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const selectedCount = document.querySelectorAll('input[name="item_ids[]"]:checked').length;
                    const templateName = nameInput.value.trim();
                    
                    if (selectedCount === 0) {
                        e.preventDefault();
                        alert('少なくとも1つのアイテムを選択してください');
                        return false;
                    }
                    
                    if (!templateName) {
                        e.preventDefault();
                        alert('テンプレート名を入力してください');
                        return false;
                    }
                    
                    return confirm(`テンプレート「${templateName}」を作成しますか？\n選択されたアイテム数: ${selectedCount}個`);
                });
            }
        });
    </script>
</body>
</html>

<?php
// セッションデータをクリア
unset($_SESSION['errors']);
unset($_SESSION['old_input']);
?>