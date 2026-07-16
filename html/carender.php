<?php
    require_once "../backend/session.php";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>カレンダー予定表</title>

    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/carender.css">
</head>

<body>

    <?php include "header.php" ?>

    <!-- メインコンテンツ -->
    <main class="main-content">

        <!-- カレンダー -->
        <div class="calendar-container">

            <div class="c_header">
                <button id="prev">◀</button>
                <h1 id="month"></h1>
                <button id="next">▶</button>
            </div>

            <div class="weekdays">
                <div>日</div>
                <div>月</div>
                <div>火</div>
                <div>水</div>
                <div>木</div>
                <div>金</div>
                <div>土</div>
            </div>

            <div id="calendar" class="calendar"></div>

        </div>

    </main>

    <!-- モーダル -->
    <div id="modal" class="modal hidden">

        <div class="modal-content">

            <h2 id="selected-date"></h2>

            <div class="input-card">

                <!-- タバコセクション -->
                <div class="input-section">
                    <div class="section-header">
                        <span class="section-icon smoke-icon">🚬</span>
                        <span class="section-label">タバコ</span>
                    </div>
                    <div class="field-row">
                        <input class="field-input" type="text" id="brand-input" placeholder="銘柄　　　　　　例：マールボロ">
                    </div>
                    <div class="field-row">
                        <input class="field-input" type="number" id="smoke-input" placeholder="本数" min="0">
                    </div>
                </div>

                <hr class="section-divider">

                <!-- アルコールセクション -->
                <div class="input-section">
                    <div class="section-header">
                        <span class="section-icon alc-icon">🍶</span>
                        <span class="section-label">アルコール</span>
                    </div>
                    <div class="field-row">
                        <input class="field-input" type="number" id="degree-input" placeholder="度数（%）" min="0" max="100" step="0.1">
                    </div>
                    <div class="field-row">
                        <input class="field-input" type="number" id="alcohol-input" placeholder="量（ml）" min="0">
                    </div>
                </div>

            </div>

            <div class="modal-buttons">
                <button id="save-btn">保存</button>
                <button id="delete-btn">削除</button>
                <button id="close-btn">閉じる</button>
            </div>

        </div>

    </div>

<!-- 警告モーダル -->
<div id="warning-modal" class="modal hidden">

    <div class="modal-content warning-modal-content">

        <div class="warning-top">

            <div class="warning-gauge">
                <svg viewBox="0 0 120 120" class="warning-gauge-svg">
                    <circle cx="60" cy="60" r="50" class="gauge-track"></circle>
                    <circle id="warning-gauge-arc" cx="60" cy="60" r="50" class="gauge-arc"></circle>
                </svg>
                <div class="warning-gauge-center">
                    <span id="warning-level-badge" class="warning-level-badge"></span>
                </div>
            </div>

            <div class="warning-summary">
                <h2>⚠ 健康警告</h2>
                <p id="warning-message"></p>
            </div>

        </div>

        <div class="warning-breakdown">
            <div class="breakdown-row">
                <span class="breakdown-label">🚬 タバコ</span>
                <div class="breakdown-bar-track">
                    <div id="breakdown-bar-smoke" class="breakdown-bar-fill smoke"></div>
                </div>
                <span id="breakdown-smoke-percent" class="breakdown-percent"></span>
            </div>
            <div class="breakdown-row">
                <span class="breakdown-label">🍺 アルコール</span>
                <div class="breakdown-bar-track">
                    <div id="breakdown-bar-alcohol" class="breakdown-bar-fill alcohol"></div>
                </div>
                <span id="breakdown-alcohol-percent" class="breakdown-percent"></span>
            </div>
        </div>

        <div class="warning-tabs">
            <button class="warning-tab active" data-tab="image">参考画像</button>
            <button class="warning-tab" data-tab="video">解説動画</button>
        </div>

        <div class="warning-tab-panel" data-panel="image">
            <div id="warning-image-scroll">
                <img id="warning-image" src="" alt="warning">
                <img id="warning-image2" src="" alt="warning2">
            </div>
        </div>

        <div class="warning-tab-panel hidden" data-panel="video">
            <div id="warning-video-wrap">
                <iframe
                    id="warning-video"
                    src=""
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                ></iframe>
            </div>
        </div>

        <div class="modal-buttons">
            <button id="warning-close-btn">閉じる</button>
        </div>

    </div>

</div>

    <script src="../javascript/carender.js?v=2"></script>

</body>
</html>