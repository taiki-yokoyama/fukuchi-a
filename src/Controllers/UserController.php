<?php

require_once __DIR__ . '/../Application/Usecase/UserRegisterUsecase.php';
require_once __DIR__ . '/../Infrastructure/UserRepositoryImpl.php';

class UserController {
    private UserRegisterUsecase $userRegisterUsecase;

    public function __construct() {
        $userRepository = new UserRepositoryImpl();
        $this->userRegisterUsecase = new UserRegisterUsecase($userRepository);
    }

    /**
     * ログイン画面を表示
     */
    public function showLogin(): void {
        // 既にログイン済みの場合はトップページにリダイレクト
        if ($this->isLoggedIn()) {
            header('Location: /');
            exit;
        }
        
        include __DIR__ . '/../View/login.php';
    }

    /**
     * ユーザー登録画面を表示
     */
    public function showRegister(): void {
        // 既にログイン済みの場合はトップページにリダイレクト
        if ($this->isLoggedIn()) {
            header('Location: /');
            exit;
        }
        
        include __DIR__ . '/../View/register.php';
    }

    /**
     * ログイン処理
     */
    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $errors = [];

        try {
            // 入力値のバリデーション
            if (empty(trim($email))) {
                $errors[] = 'メールアドレスは必須です';
            }
            
            if (empty($password)) {
                $errors[] = 'パスワードは必須です';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_input'] = ['email' => $email];
                header('Location: /login');
                exit;
            }

            // 認証処理
            $user = $this->userRegisterUsecase->authenticateUser($email, $password);
            
            if ($user === null) {
                $errors[] = 'メールアドレスまたはパスワードが正しくありません';
                $_SESSION['errors'] = $errors;
                $_SESSION['old_input'] = ['email' => $email];
                header('Location: /login');
                exit;
            }

            // セッションにユーザー情報を保存
            $this->setUserSession($user);
            
            // CSRFトークンを生成
            $this->generateCsrfToken();
            
            $_SESSION['success'] = 'ログインしました';
            header('Location: /');
            exit;

        } catch (Exception $e) {
            $errors[] = 'ログイン処理中にエラーが発生しました';
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = ['email' => $email];
            header('Location: /login');
            exit;
        }
    }

    /**
     * ユーザー登録処理
     */
    public function register(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $errors = [];

        try {
            // パスワード確認のバリデーション
            if ($password !== $passwordConfirm) {
                $errors[] = 'パスワードが一致しません';
            }

            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_input'] = ['name' => $name, 'email' => $email];
                header('Location: /register');
                exit;
            }

            // ユーザー登録
            $user = $this->userRegisterUsecase->registerUser($name, $email, $password);
            
            // セッションにユーザー情報を保存
            $this->setUserSession($user);
            
            // CSRFトークンを生成
            $this->generateCsrfToken();
            
            $_SESSION['success'] = 'ユーザー登録が完了しました';
            header('Location: /');
            exit;

        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = ['name' => $name, 'email' => $email];
            header('Location: /register');
            exit;
        } catch (Exception $e) {
            $errors[] = 'ユーザー登録中にエラーが発生しました';
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = ['name' => $name, 'email' => $email];
            header('Location: /register');
            exit;
        }
    }

    /**
     * ログアウト処理
     */
    public function logout(): void {
        // CSRFトークンの検証
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            header('Location: /');
            exit;
        }

        // セッションを破棄
        session_destroy();
        
        // 新しいセッションを開始
        session_start();
        session_regenerate_id(true);
        
        $_SESSION['success'] = 'ログアウトしました';
        header('Location: /login');
        exit;
    }

    /**
     * ログイン状態をチェック
     */
    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * 現在のユーザーを取得
     */
    public function getCurrentUser(): ?User {
        if (!$this->isLoggedIn()) {
            return null;
        }

        try {
            return $this->userRegisterUsecase->getUserById($_SESSION['user_id']);
        } catch (Exception $e) {
            // ユーザーが見つからない場合はセッションをクリア
            unset($_SESSION['user_id']);
            return null;
        }
    }

    /**
     * セッションにユーザー情報を設定
     */
    private function setUserSession(User $user): void {
        // セッションIDを再生成（セッションハイジャック対策）
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['user_name'] = $user->getName();
        $_SESSION['user_email'] = $user->getEmail();
    }

    /**
     * CSRFトークンを生成
     */
    private function generateCsrfToken(): void {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    /**
     * CSRFトークンを検証
     */
    private function verifyCsrfToken(string $token): bool {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * 認証が必要なページへのアクセス制御
     */
    public function requireAuth(): void {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: /login');
            exit;
        }
    }

    /**
     * CSRFトークンを取得
     */
    public function getCsrfToken(): string {
        $this->generateCsrfToken();
        return $_SESSION['csrf_token'];
    }
}