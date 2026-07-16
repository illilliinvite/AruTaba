<?php
    require_once "../backend/session.php";
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <title>Chat UI</title>

  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="../css/profile_setting.css" />
</head>

<body>

  <?php include "header.php" ?>

  <!-- サイドメニュー -->
  <nav class="sidebar" id="sidebar">

    <ul>
        <a href="home.html">
          <li><i class="fa-solid fa-house"></i> ホーム</li>
        </a>  
        <a href="friend.html">
          <li><i class="fa-solid fa-users"></i> フレンド</li>
        </a>
        <a href="carender.html">  
          <li><i class="fa-solid fa-calendar-days"></i> カレンダー</li>
        </a>  
        <a href="keiziban.html">
          <li><i class="fa-solid fa-clipboard-list"></i> 掲示板</li>
        </a>  
        <a href="suuti.html">
          <li><i class="fa-solid fa-keyboard"></i> 数値入力</li>
        </a>  
        <a href="notice.html">
          <li><i class="fa-solid fa-bell"></i> お知らせ</li>
        </a>  
        <a href="profile_setting.html">
          <li><i class="fa-solid fa-gear"></i> プロフィール設定</li>
        </a>  
      </ul>

      <a href="login.html" class="logout">
        <i class="fa-solid fa-right-from-bracket"></i> ログアウト
      </a>
  </nav>

  <!-- 中央要素 -->
  <div class="container">
    <div class="card">

      <!-- アイコン（クリックで画像選択 → 選択時に即アップロード） -->
      <div class="icon-section">
        <div class="icon" id="iconWrapper" tabindex="0" role="button" aria-label="プロフィール写真を変更">
          <img id="iconImage" src="" alt="" style="display:none;">
          <span id="iconPlaceholder"><i class="fa-solid fa-user"></i></span>
          <div class="icon-overlay">
            <i class="fa-solid fa-camera"></i>
            <span>変更</span>
          </div>
          <input type="file"
                 id="iconInput"
                 name="icon_image"
                 accept="image/png, image/jpeg, image/gif, image/webp"
                 style="display:none;">
        </div>
        <p class="icon-hint">タップして写真を変更</p>
      </div>

      <form class="form" id="profileForm" method="post" action="profile_setting.php">

        <div class="form-section">
          <h2 class="section-title"><i class="fa-solid fa-user"></i> プロフィール</h2>

          <div class="field">
            <span class="field-label">名前</span>
            <div class="input-wrap">
              <i class="fa-solid fa-user field-icon"></i>
              <input type="text" name="user_name" placeholder="山田 太郎">
            </div>
          </div>

          <div class="field">
            <span class="field-label">メールアドレス</span>
            <div class="input-wrap">
              <i class="fa-solid fa-envelope field-icon"></i>
              <input type="email" name="mail_address" placeholder="you@example.com">
            </div>
          </div>

          <div class="field">
            <span class="field-label">パスワード</span>
            <div class="input-wrap">
              <i class="fa-solid fa-lock field-icon"></i>
              <input type="password" name="password" id="passwordInput" placeholder="変更する場合のみ入力">
              <i class="fa-regular fa-eye toggle-icon" id="togglePassword" role="button" aria-label="パスワードを表示"></i>
            </div>
          </div>
        </div>

        <div class="form-section">
          <h2 class="section-title accent"><i class="fa-solid fa-heart-pulse"></i> 生活習慣の上限</h2>
          <p class="section-desc">1日あたりの目標を設定すると、超えたときにお知らせします</p>

          <div class="field">
            <span class="field-label">喫煙上限（本/日）</span>
            <div class="input-wrap">
              <i class="fa-solid fa-smoking field-icon"></i>
              <input type="number" name="smoking_limit" placeholder="5" min="0">
              <span class="unit">本/日</span>
            </div>
          </div>

          <div class="field">
            <span class="field-label">飲酒上限（mL/日）</span>
            <div class="input-wrap">
              <i class="fa-solid fa-wine-glass field-icon"></i>
              <input type="number" name="drinking_limit" placeholder="350" min="0">
              <span class="unit">mL/日</span>
            </div>
          </div>
        </div>

        <button type="submit"><i class="fa-solid fa-check"></i> 登録する</button>

        <div class="danger-zone">
          <p class="danger-title">アカウントの削除</p>
          <p class="danger-desc">削除するとすべてのデータが完全に失われ、元に戻すことはできません</p>
          <button type="button" class="delete-account-btn" id="openDeleteModalBtn">
            <i class="fa-solid fa-trash"></i> アカウントを削除
          </button>
        </div>

      </form>

    </div>
  </div>
  <div id="message"></div>

  <!-- 削除確認モーダル -->
  <div class="modal-overlay" id="deleteModalOverlay">
    <div class="modal-box">
      <h3>本当にアカウントを削除しますか？</h3>
      <p>この操作は取り消せません。プロフィール・カレンダー記録・フレンド・掲示板の投稿などすべてのデータが削除されます。続けるにはパスワードを入力してください。</p>
      <input type="password" id="deleteConfirmPassword" placeholder="パスワード">
      <div class="modal-actions">
        <button type="button" class="modal-cancel-btn" id="cancelDeleteBtn">キャンセル</button>
        <button type="button" class="modal-confirm-btn" id="confirmDeleteBtn">削除する</button>
      </div>
    </div>
  </div>

  <!-- フッター -->
  <footer class="footer"></footer>
  <script src="../javascript/profile_setting.js"></script>
  <script src="../javascript/main.js"></script>

</body>
</html>