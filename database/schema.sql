-- Users テーブル
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL
);

-- Baggages テーブル
CREATE TABLE IF NOT EXISTS baggages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    date TEXT,
    is_template INTEGER DEFAULT 0,
    name TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Items テーブル
CREATE TABLE IF NOT EXISTS items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    tag_id TEXT UNIQUE,
    image TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Baggage_Items 中間テーブル
CREATE TABLE IF NOT EXISTS baggage_items (
    baggage_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    PRIMARY KEY (baggage_id, item_id),
    FOREIGN KEY (baggage_id) REFERENCES baggages(id),
    FOREIGN KEY (item_id) REFERENCES items(id)
);

-- インデックスの作成
CREATE INDEX IF NOT EXISTS idx_baggages_user_date ON baggages(user_id, date);
CREATE INDEX IF NOT EXISTS idx_baggages_user_template ON baggages(user_id, is_template);
CREATE INDEX IF NOT EXISTS idx_items_user ON items(user_id);
CREATE INDEX IF NOT EXISTS idx_items_tag ON items(tag_id);