<?php
    require_once "../backend/session.php";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ホーム</title>
  <link rel="stylesheet" href="../css/header.css">
  <link rel="stylesheet" href="../css/home.css" />
</head>


<body>

<?php include "header.php" ?>

<main>
  <div class="top-row">
    
    <section class="card ranking-card">
      <div class="ranking-header">
        <h2>
          <!--  <a href="ranking.html" class="ranking-link">  -->
            <i class="fa-solid fa-trophy"></i> ランキング
          <!--  </a>  -->
        </h2>

        <div class="ranking-tabs">
          <button class="tab-button active" id="tab-tobacco">
            タバコ
          </button>

          <button class="tab-button" id="tab-alcohol" id="tab-tobacco">
            アルコール
          </button>
        </div>

      </div>
      <ul class="ranking-list" id="ranking-list">
      </ul>
    </section> 


    <div class="right-column">
      <section class="card notice-card">
        <h2>
          <i class="fa-solid fa-bell"></i> 
          <a href="notice.php" class="notice-link">
             お知らせ
          </a>
        </h2>
        <p class="notice-label">最新情報</p>
        <div class="notice-days">サービス終了のお知らせ</div>
      </section> 
      
      <section class="card record-card">
  <h2><i class="fa-solid fa-pen-to-square"></i> 記録</h2>

  <p class="record-label" id="record-label">
    禁煙継続日数
  </p>

  <div class="record-days" id="record-days">
    
  </div>
</section>
    </div>
  
    
  </div> <section class="card quick-access-card">
    <h2><i class="fa-solid fa-thumbtack"></i> メニュー</h2>
    <div class="quick-grid">
      <a href="friend.php" class="quick-item"><div class="icon-circle icon-green"><i class="fa-solid fa-users"></i></div><h3>フレンド一覧</h3><p>フレンドを確認</p></a>
      <a href="carender.php" class="quick-item"><div class="icon-circle icon-blue"><i class="fa-solid fa-calendar-days"></i></div><h3>カレンダー</h3><p>過去の記録の閲覧・更新</p></a>
      <a href="keiziban.php" class="quick-item"><div class="icon-circle icon-purple"><i class="fa-solid fa-clipboard-list"></i></div><h3>掲示板</h3><p>様々な人と交流</p></a>
      <a href="suuti.php" class="quick-item" id="suuti-guide"><div class="icon-circle icon-gold"><i class="fa-solid fa-keyboard"></i></div><h3>数値入力</h3><p>今日の記録を入力</p></a>
      <a href="profile_setting.php" class="quick-item"><div class="icon-circle icon-dark"><i class="fa-solid fa-gear"></i></div><h3>設定</h3><p>各種設定を変更</p></a>
    </div>
  </section> 

  <div id="guide-modal" class="guide-modal">
  <div class="guide-modal-bar">
    <div class="guide-modal-bar-icon">📝</div>
    <div class="guide-modal-bar-title">今日の記録が未入力です</div>
  </div>
  <div class="guide-modal-body">
    <p>
      今日の喫煙量・飲酒量がまだ入力されていません。<br>
      「数値入力」から今日の記録を入力しましょう。
    </p>
    <button id="guide-ok-btn">OK、入力する</button>
  </div>
</div>

  <div id="guide-overlay"></div>

</main>

  <script src="../javascript/main.js"></script>
  <script src="../javascript/home.js"></script>
</body>
</html>