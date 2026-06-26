<!-- ヘッダー -->
<header class="header">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- ハンバーガーメニュー -->
    <div class="hamburger" id="hamburger">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <!-- タイトル -->
    <div class="logo">
      <a href="home.php">AruTaba</a>
    </div>

    <div class="header-profile">
        <img id="headerProfileImage" src="../images/default_icon.png" alt="プロフィール画像" style="display:none;">
        <i id="headerProfileDefault"class="fa-solid fa-user"></i>
    </div>

</header>

  <!-- サイドメニュー -->
  <nav class="sidebar" id="sidebar">
      <ul>
        <a href="home.php">
          <li><i class="fa-solid fa-house"></i> ホーム</li>
        </a>  
        <a href="friend.php">
          <li><i class="fa-solid fa-users"></i> フレンド</li>
        </a>
        <a href="carender.php">  
          <li><i class="fa-solid fa-calendar-days"></i> カレンダー</li>
        </a>  
        <a href="keiziban.php">
          <li><i class="fa-solid fa-clipboard-list"></i> 掲示板</li>
        </a>  
        <a href="suuti.php">
          <li><i class="fa-solid fa-keyboard"></i> 数値入力</li>
        </a>  
        <a href="notice.php">
          <li><i class="fa-solid fa-bell"></i> お知らせ</li>
        </a>  
        <a href="profile_setting.php">
          <li><i class="fa-solid fa-gear"></i> プロフィール設定</li>
        </a>  
      </ul>

      <a href="login.php" class="logout">
        <i class="fa-solid fa-right-from-bracket"></i> ログアウト
      </a>
  </nav>

<script src="../javascript/main.js"></script>
<link rel="stylesheet" href="../css/header.css">
