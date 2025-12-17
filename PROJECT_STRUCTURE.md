# プロジェクト構造

## ディレクトリ構成

```
src/
├── Application/
│   └── Usecase/           # アプリケーションサービス（ユースケース）
├── Config/
│   └── config.php         # アプリケーション設定
├── Controllers/           # プレゼンテーション層のコントローラー
├── Domain/
│   ├── Entity/           # ドメインエンティティ
│   ├── Repository/       # リポジトリインターフェース
│   └── ValueObject/      # 値オブジェクト
├── Infrastructure/       # インフラストラクチャ層の実装
├── View/                # ビューテンプレート
└── autoload.php         # クラスオートローダー

database/
├── schema.sql           # データベーススキーマ
├── init.php            # データベース初期化スクリプト
└── database.db         # SQLiteデータベースファイル（自動生成）
```

## ドメインモデル

### エンティティ
- **User**: ユーザー情報の管理
- **Baggage**: 持ち物セットの管理（テンプレート機能含む）
- **Item**: 個別アイテムの管理
- **BaggageItem**: 持ち物セットとアイテムの関連付け

### 値オブジェクト
- **ItemName**: アイテム名のバリデーション
- **TagId**: RFIDタグIDのバリデーション

### リポジトリ
- **UserRepository**: ユーザーデータアクセス
- **BaggageRepository**: 持ち物セットデータアクセス
- **ItemRepository**: アイテムデータアクセス
- **BaggageItemRepository**: 関連付けデータアクセス

## 使用方法

1. データベース初期化:
   ```php
   php database/init.php
   ```

2. 構造テスト:
   ```php
   php test_structure.php
   ```

3. オートローダーの使用:
   ```php
   require_once 'src/autoload.php';
   // これで全てのクラスが自動的に読み込まれます
   ```

## 設計原則

- **ドメイン駆動設計（DDD）**: ビジネスロジックをドメイン層に集約
- **依存性逆転**: インフラストラクチャ層がドメイン層に依存
- **単一責任原則**: 各クラスは単一の責任を持つ
- **値オブジェクト**: 不変性とバリデーションを保証