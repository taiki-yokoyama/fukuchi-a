<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アイテム登録 - 忘れ物防止アプリ</title>
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
                    <a href="/tag/register" class="text-gray-600 hover:text-gray-900">タグ登録</a>
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
            <h1 class="text-3xl font-bold text-gray-900">アイテム登録</h1>
            <p class="mt-2 text-gray-600">持ち物として管理するアイテムを登録します</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- 登録フォーム -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">新しいアイテムを登録</h2>
                <form method="POST" action="/item/register" enctype="multipart/form-data">
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">アイテム名 <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm px-3 py-2 border"
                                placeholder="例: 財布、鍵、スマートフォン">
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">画像（任意）</label>
                            <input type="file" name="image" id="image" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100">
                            <p class="mt-1 text-sm text-gray-500">JPG, PNG, GIF形式 (最大5MB)</p>
                        </div>

                        <div class="pt-4">
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                アイテムを登録
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 登録済みアイテム一覧 -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">登録済みアイテム</h2>
                <?php if (empty($items)): ?>
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">アイテムがまだ登録されていません</p>
                    </div>
                <?php else: ?>
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($items as $item): ?>
                            <li class="py-4 flex items-center">
                                <?php if ($item->getImage()): ?>
                                    <img src="<?= htmlspecialchars($item->getImage()) ?>" alt="<?= htmlspecialchars($item->getName()->getName()) ?>" class="h-12 w-12 rounded-full object-cover">
                                <?php else: ?>
                                    <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item->getName()->getName()) ?></p>
                                    <?php if ($item->hasTag()): ?>
                                        <p class="text-xs text-green-600">タグID: <?= htmlspecialchars($item->getTagId()->getValue()) ?></p>
                                    <?php else: ?>
                                        <p class="text-xs text-yellow-600">タグ未割り当て</p>
                                    <?php endif; ?>
                                </div>
                                <?php if (!$item->hasTag()): ?>
                                    <a href="/tag/register" class="text-indigo-600 hover:text-indigo-900 text-sm">タグ割り当て</a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
