<?php
// pdo作成
$pdo = new PDO('sqlite:' . __DIR__ . '/../../database/database.db');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
return $pdo;
