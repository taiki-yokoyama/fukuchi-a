<?php

require_once __DIR__ . '/../../Domain/Repository/UserRepository.php';
require_once __DIR__ . '/../../Domain/Entity/User.php';

class UserRegisterUsecase {
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * ユーザーを登録する
     * 
     * @param string $name ユーザー名
     * @param string $email メールアドレス
     * @param string $password パスワード
     * @return User 作成されたユーザー
     */
    public function registerUser(string $name, string $email, string $password): User {
        // メールアドレスの重複チェック
        $existingUser = $this->userRepository->findByEmail($email);
        if ($existingUser !== null) {
            throw new InvalidArgumentException("このメールアドレスは既に使用されています");
        }

        // 入力値のバリデーション
        $this->validateUserInput($name, $email, $password);

        // パスワードをハッシュ化
        $hashedPassword = User::hashPassword($password);

        // ユーザーエンティティを作成
        $user = new User(null, $name, $email, $hashedPassword);

        // ユーザーを保存
        $this->userRepository->save($user);

        return $user;
    }

    /**
     * ユーザー認証を行う
     * 
     * @param string $email メールアドレス
     * @param string $password パスワード
     * @return ?User 認証されたユーザー（認証失敗時はnull）
     */
    public function authenticateUser(string $email, string $password): ?User {
        $user = $this->userRepository->findByEmail($email);
        
        if ($user === null) {
            return null;
        }

        if ($user->verifyPassword($password)) {
            return $user;
        }

        return null;
    }

    /**
     * ユーザー入力のバリデーション
     * 
     * @param string $name ユーザー名
     * @param string $email メールアドレス
     * @param string $password パスワード
     * @return void
     */
    private function validateUserInput(string $name, string $email, string $password): void {
        // 名前のバリデーション
        if (empty(trim($name))) {
            throw new InvalidArgumentException("ユーザー名は必須です");
        }
        
        if (strlen($name) > 100) {
            throw new InvalidArgumentException("ユーザー名は100文字以内で入力してください");
        }

        // メールアドレスのバリデーション
        if (empty(trim($email))) {
            throw new InvalidArgumentException("メールアドレスは必須です");
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("有効なメールアドレスを入力してください");
        }

        // パスワードのバリデーション
        if (empty($password)) {
            throw new InvalidArgumentException("パスワードは必須です");
        }
        
        if (strlen($password) < 8) {
            throw new InvalidArgumentException("パスワードは8文字以上で入力してください");
        }
    }

    /**
     * ユーザーIDでユーザーを取得する
     * 
     * @param int $userId ユーザーID
     * @return ?User ユーザー（見つからない場合はnull）
     */
    public function getUserById(int $userId): ?User {
        return $this->userRepository->findById($userId);
    }
}