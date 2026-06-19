<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'ログイン情報がありません']);
    exit;
}

if (empty($_POST['mail'])) {
    echo json_encode(['success' => false, 'error' => 'mail がありません']);
    exit;
}

if (empty($_FILES['file'])) {
    echo json_encode(['success' => false, 'error' => 'ファイルがありません']);
    exit;
}

$friend_mail = trim($_POST['mail']);
$my_user_id = (string)$_SESSION['user_id'];
$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'アップロードに失敗しました(code: ' . $file['error'] . ')']);
    exit;
}

$MAX_SIZE = 20 * 1024 * 1024;
if ($file['size'] > $MAX_SIZE) {
    echo json_encode(['success' => false, 'error' => 'ファイルサイズが大きすぎます']);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

$allowed = [
    'image/jpeg' => ['type' => 'image', 'ext' => 'jpg'],
    'image/png'  => ['type' => 'image', 'ext' => 'png'],
    'image/gif'  => ['type' => 'image', 'ext' => 'gif'],
    'image/webp' => ['type' => 'image', 'ext' => 'webp'],
    'video/mp4'  => ['type' => 'video', 'ext' => 'mp4'],
    'video/webm' => ['type' => 'video', 'ext' => 'webm'],
];

if (!isset($allowed[$mime])) {
    echo json_encode(['success' => false, 'error' => '対応していないファイル形式です(' . $mime . ')']);
    exit;
}

$message_type = $allowed[$mime]['type'];
$ext = $allowed[$mime]['ext'];

$new_filename = bin2hex(random_bytes(16)) . '.' . $ext;

$upload_dir = __DIR__ . '/../public/uploads/friend_chat/';
$dest_path = $upload_dir . $new_filename;
$db_path = 'public/uploads/friend_chat/' . $new_filename;

if (!move_uploaded_file($file['tmp_name'], $dest_path)) {
    echo json_encode(['success' => false, 'error' => '保存に失敗しました(権限を確認してください)']);
    exit;
}

try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=arutaba;charset=utf8mb4',
        'root',
        'arutaba',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    $stmt = $pdo->prepare("SELECT user_id FROM profile WHERE mail_address = :mail");
    $stmt->bindValue(':mail', $friend_mail, PDO::PARAM_STR);
    $stmt->execute();
    $friend = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$friend) {
        unlink($dest_path);
        echo json_encode(['success' => false, 'error' => '相手が見つかりません']);
        exit;
    }

    $friend_user_id = $friend['user_id'];
    $message_id = uniqid('msg_', true);

    $stmt = $pdo->prepare("
        INSERT INTO friend_chat
        (user_id, receiver_id, message_id, message_type, chat_history, file_path, file_name, is_read)
        VALUES
        (:user_id, :receiver_id, :message_id, :message_type, '', :file_path, :file_name, 0)
    ");

    $stmt->execute([
        ':user_id' => $my_user_id,
        ':receiver_id' => $friend_user_id,
        ':message_id' => $message_id,
        ':message_type' => $message_type,
        ':file_path' => $db_path,
        ':file_name' => $file['name'],
    ]);

    echo json_encode([
        'success' => true,
        'id' => $pdo->lastInsertId()
    ]);

} catch (PDOException $e) {
    unlink($dest_path);
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => 'サーバーエラー']);
}