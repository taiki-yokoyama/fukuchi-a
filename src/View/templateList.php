<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>テンプレート一覧 - 忘れ物防止アプリ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- ナビゲーションバー -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="text-xl font-semibold text-gray-900 hover:text-blue-600">忘れ物防止アプリ</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="/" class="text-gray-700 hover:text-blue-600">トップページ</a>
                    <a href="/baggage/register" class="text-gray-700 hover:text-blue-600">持ち物登録</a>
                    <form method="POST" action="/logout" class="inline">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
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
    <main class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <!-- ページタイトル -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">テンプレート一覧</h1>
                        <p class="mt-2 text-gray-600">作成済みのテンプレートを管理できます</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="/template/create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            新しいテンプレート
                        </a>
                        <a href="/template/select" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 100-4 2 2 0 000 4zm6 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            テンプレートを適用
                        </a>
                    </div>
                </div>
            </div>

            <!-- 成功メッセージ表示 -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                <?php echo htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

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

            <!-- テンプレート一覧 -->
            <?php if (empty($templates)): ?>
                <div class="text-center py-12 bg-white rounded-lg shadow">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                        <path d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A9.971 9.971 0 0124 24c4.21 0 7.813 2.602 9.288 6.286" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">テンプレートがありません</h3>
                    <p class="mt-1 text-sm text-gray-500">まずはテンプレートを作成してください</p>
                    <div class="mt-6">
                        <a href="/template/create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            テンプレートを作成
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($templates as $template): ?>
                        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <!-- テンプレート名 -->
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <?php echo htmlspecialchars($template->getName(), ENT_QUOTES, 'UTF-8'); ?>
                                    </h3>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        テンプレート
                                    </span>
                                </div>

                                <!-- アイテム数 -->
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium"><?php echo count($template->getItems()); ?>個</span>のアイテムが含まれています
                                    </p>
                                </div>

                                <!-- アイテム一覧（最初の3つまで表示） -->
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">含まれるアイテム:</h4>
                                    <div class="space-y-1">
                                        <?php 
                                        $items = $template->getItems();
                                        $displayItems = array_slice($items, 0, 3);
                                        foreach ($displayItems as $item): 
                                        ?>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="w-3 h-3 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                                <?php echo htmlspecialchars($item->getName()->getName(), ENT_QUOTES, 'UTF-8'); ?>
                                                <?php if ($item->hasTag()): ?>
                                                    <span class="ml-2 inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        タグ有
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                        
                                        <?php if (count($items) > 3): ?>
                                            <p class="text-xs text-gray-500 mt-1">
                                                他 <?php echo count($items) - 3; ?>個のアイテム
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- アクションボタン -->
                                <div class="flex space-x-2">
                                    <a href="/template/select?template_id=<?php echo $template->getId(); ?>" 
                                       class="flex-1 inline-flex items-center justify-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 8a2 2 0 100-4 2 2 0 000 4zm6 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        適用
                                    </a>
                                    <button 
                                        type="button" 
                                        class="px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                        onclick="showTemplateDetails(<?php echo htmlspecialchars(json_encode([
                                            'id' => $template->getId(),
                                            'name' => $template->getName(),
                                            'items' => array_map(function($item) {
                                                return [
                                                    'name' => $item->getName()->getName(),
                                                    'hasTag' => $item->hasTag(),
                                                    'tagId' => $item->hasTag() ? $item->getTagId()->getId() : null
                                                ];
                                            }, $template->getItems())
                                        ]), ENT_QUOTES, 'UTF-8'); ?>)"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- テンプレート詳細モーダル -->
    <div id="template-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">テンプレート詳細</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeTemplateModal()">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">含まれるアイテム:</h4>
                    <div id="modal-items" class="space-y-2 max-h-60 overflow-y-auto">
                        <!-- アイテム一覧がここに動的に挿入されます -->
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            onclick="closeTemplateModal()">
                        閉じる
                    </button>
                    <a id="modal-apply-link" 
                       href="#" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        このテンプレートを適用
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTemplateDetails(template) {
            const modal = document.getElementById('template-modal');
            const title = document.getElementById('modal-title');
            const itemsContainer = document.getElementById('modal-items');
            const applyLink = document.getElementById('modal-apply-link');
            
            title.textContent = `テンプレート: ${template.name}`;
            applyLink.href = `/template/select?template_id=${template.id}`;
            
            // アイテム一覧を生成
            itemsContainer.innerHTML = '';
            template.items.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'flex items-center justify-between p-2 bg-gray-50 rounded';
                
                const tagBadge = item.hasTag 
                    ? `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                         タグ有 (${item.tagId})
                       </span>`
                    : `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                         タグ無
                       </span>`;
                
                itemDiv.innerHTML = `
                    <span class="text-sm text-gray-900">${item.name}</span>
                    ${tagBadge}
                `;
                
                itemsContainer.appendChild(itemDiv);
            });
            
            modal.classList.remove('hidden');
        }
        
        function closeTemplateModal() {
            const modal = document.getElementById('template-modal');
            modal.classList.add('hidden');
        }
        
        // モーダル外クリックで閉じる
        document.getElementById('template-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTemplateModal();
            }
        });
    </script>
</body>
</html>