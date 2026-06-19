<?php

session_start();

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$mail = $_GET['mail'] ?? '';

if ($mail === '') {
    echo json_encode([]);
    exit;
}

try {

    $pdo = new PDO(
        'mysql:host=localhost;dbname=arutaba;charset=utf8mb4',
        'root',
        'arutaba',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    $my_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        SELECT user_id
        FROM profile
        WHERE mail_address = :mail
    ");

    $stmt->execute([
        ':mail' => $mail
    ]);

    $friend = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$friend) {
        echo json_encode([]);
        exit;
    }

    $friend_id = $friend['user_id'];

    $stmt = $pdo->prepare("
        SELECT id
        FROM friend_chat
        WHERE user_id = :my_id
          AND receiver_id = :friend_id
          AND is_read = 1
    ");

    $stmt->execute([
        ':my_id' => $my_id,
        ':friend_id' => $friend_id
    ]);

    echo json_encode(
        $stmt->fetchAll(PDO::FETCH_COLUMN)
    );

} catch (Exception $e) {

    echo json_encode([]);
}