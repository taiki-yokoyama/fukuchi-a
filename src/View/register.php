<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録 - 忘れ物防止アプリ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 p-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                ユーザー登録
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                既にアカウントをお持ちの方は
                <a href="/login" class="font-medium text-blue-600 hover:text-blue-500">
                    ログイン
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

        <form class="mt-8 space-y-6" action="/register" method="POST" id="registerForm">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">ユーザー名</label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        autocomplete="name" 
                        required 
                        maxlength="100"
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                        placeholder="ユーザー名を入力"
                        value="<?php echo htmlspecialchars($_SESSION['old_input']['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >
                    <div id="nameError" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                    <input 
                        id="email" 
                        name="email" 
                        type="email" 
                        autocomplete="email" 
                        required 
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                        placeholder="メールアドレスを入力"
                        value="<?php echo htmlspecialchars($_SESSION['old_input']['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    >
                    <div id="emailError" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">パスワード</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        autocomplete="new-password" 
                        required 
                        minlength="8"
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                        placeholder="8文字以上のパスワード"
                    >
                    <div id="passwordError" class="text-red-600 text-sm mt-1 hidden"></div>
                    <p class="mt-1 text-sm text-gray-500">8文字以上で入力してください</p>
                </div>

                <div>
                    <label for="password_confirm" class="block text-sm font-medium text-gray-700">パスワード確認</label>
                    <input 
                        id="password_confirm" 
                        name="password_confirm" 
                        type="password" 
                        autocomplete="new-password" 
                        required 
                        class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" 
                        placeholder="パスワードを再入力"
                    >
                    <div id="passwordConfirmError" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    id="submitButton"
                >
                    ユーザー登録
                </button>
            </div>
        </form>
    </div>

    <script>
        // フォームバリデーション
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirm');
            const submitButton = document.getElementById('submitButton');
            let hasErrors = false;

            // エラーメッセージをクリア
            clearErrors();

            // ユーザー名のバリデーション
            if (!name.value.trim()) {
                showError('nameError', 'ユーザー名は必須です');
                hasErrors = true;
            } else if (name.value.length > 100) {
                showError('nameError', 'ユーザー名は100文字以内で入力してください');
                hasErrors = true;
            }

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
            } else if (password.value.length < 8) {
                showError('passwordError', 'パスワードは8文字以上で入力してください');
                hasErrors = true;
            }

            // パスワード確認のバリデーション
            if (!passwordConfirm.value) {
                showError('passwordConfirmError', 'パスワード確認は必須です');
                hasErrors = true;
            } else if (password.value !== passwordConfirm.value) {
                showError('passwordConfirmError', 'パスワードが一致しません');
                hasErrors = true;
            }

            if (hasErrors) {
                e.preventDefault();
                return false;
            }

            // 送信ボタンを無効化（二重送信防止）
            submitButton.disabled = true;
            submitButton.textContent = '登録中...';
        });

        // リアルタイムバリデーション
        document.getElementById('name').addEventListener('blur', function() {
            const name = this.value.trim();
            if (!name) {
                showError('nameError', 'ユーザー名は必須です');
            } else if (name.length > 100) {
                showError('nameError', 'ユーザー名は100文字以内で入力してください');
            } else {
                hideError('nameError');
            }
        });

        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && !isValidEmail(email)) {
                showError('emailError', '有効なメールアドレスを入力してください');
            } else if (email) {
                hideError('emailError');
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            if (password && password.length < 8) {
                showError('passwordError', 'パスワードは8文字以上で入力してください');
            } else if (password) {
                hideError('passwordError');
                // パスワード確認もチェック
                checkPasswordMatch();
            }
        });

        document.getElementById('password_confirm').addEventListener('input', function() {
            checkPasswordMatch();
        });

        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;
            
            if (passwordConfirm && password !== passwordConfirm) {
                showError('passwordConfirmError', 'パスワードが一致しません');
            } else if (passwordConfirm) {
                hideError('passwordConfirmError');
            }
        }

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
            hideError('nameError');
            hideError('emailError');
            hideError('passwordError');
            hideError('passwordConfirmError');
        }

        // ページ読み込み時にフォーカスを設定
        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            if (!nameInput.value) {
                nameInput.focus();
            }
        });
    </script>

    <?php 
    // 古い入力値をクリア
    unset($_SESSION['old_input']); 
    ?>
</body>
</html>