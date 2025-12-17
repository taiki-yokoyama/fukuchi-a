<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - 忘れ物防止アプリ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                ログイン
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                アカウントをお持ちでない方は
                <a href="/register" class="font-medium text-blue-600 hover:text-blue-500">
                    新規登録
                </a>
            </p>
        </div>

        <!-- エラーメッセージ表示 -->
        <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            エラーが発生しました
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                <?php foreach ($_SESSION['errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <!-- 成功メッセージ表示 -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-50 border border-green-200 rounded-md p-4">
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

        <form class="mt-8 space-y-6" action="/login" method="POST" id="loginForm">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">メールアドレス</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                        placeholder="メールアドレス"
                        value="<?php echo htmlspecialchars($_SESSION['old_input']['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >
                    <div id="emailError" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>
                <div>
                    <label for="password" class="sr-only">パスワード</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        autocomplete="current-password" 
                        required 
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                        placeholder="パスワード"
                    >
                    <div id="passwordError" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    id="submitButton"
                >
                    ログイン
                </button>
            </div>
        </form>
    </div>

    <script>
        // フォームバリデーション
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const submitButton = document.getElementById('submitButton');
            let hasErrors = false;

            // エラーメッセージをクリア
            clearErrors();

            // メールアドレスのバリデーション
            if (!email.value.trim()) {
                showError('emailError', 'メールアドレスは必須です');
                hasErrors = true;
            } else if (!isValidEmail(email.value)) {
                showError('emailError', '有効なメールアドレスを入力してください');
                hasErrors = true;
            }

            // パスワードのバリデーション
            if (!password.value) {
                showError('passwordError', 'パスワードは必須です');
                hasErrors = true;
            }

            if (hasErrors) {
                e.preventDefault();
                return false;
            }

            // 送信ボタンを無効化（二重送信防止）
            submitButton.disabled = true;
            submitButton.textContent = 'ログイン中...';
        });

        // リアルタイムバリデーション
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && !isValidEmail(email)) {
                showError('emailError', '有効なメールアドレスを入力してください');
            } else {
                hideError('emailError');
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            if (this.value) {
                hideError('passwordError');
            }
        });

        // ヘルパー関数
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.classList.remove('hidden');
        }

        function hideError(elementId) {
            const errorElement = document.getElementById(elementId);
            errorElement.classList.add('hidden');
        }

        function clearErrors() {
            hideError('emailError');
            hideError('passwordError');
        }

        // ページ読み込み時にフォーカスを設定
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            if (!emailInput.value) {
                emailInput.focus();
            } else {
                document.getElementById('password').focus();
            }
        });
    </script>

    <?php 
    // 古い入力値をクリア
    unset($_SESSION['old_input']); 
    ?>
</body>
</html>