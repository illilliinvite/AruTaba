<?php
    require_once "../backend/session.php";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>AruTaba - 数値入力</title>

    <!-- CSS 読み込み -->
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/suuti.css">
</head>

<body>

    <?php include "header.php" ?>

    <!-- メイン -->
    <main class="input-main">

        <form action="../backend/suuti.php" method="POST">

            <div class="input-card">

                <!-- タバコセクション -->
                <div class="input-section">
                    <div class="section-header">
                        <span class="section-icon smoke-icon">🚬</span>
                        <span class="section-label">タバコ</span>
                    </div>
                    <div class="field-row">
                        <label class="field-label" for="tobacco_brand">銘柄</label>
                        <input class="field-input" type="text" id="tobacco_brand" name="tobacco_brand" placeholder="例：マールボロ">
                    </div>
                    <div class="field-row">
                        <label class="field-label" for="tobacco_amount">本数</label>
                        <input class="field-input" type="number" id="tobacco_amount" name="tobacco_amount" placeholder="0" min="0" max="100000" required>
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
                        <label class="field-label" for="alcohol_dosuu">度数（%）</label>
                        <input class="field-input" type="number" id="alcohol_dosuu" name="alcohol_dosuu" placeholder="0" min="0" max="100" step="0.1">
                    </div>
                    <div class="field-row">
                        <label class="field-label" for="alcohol_amount">量（ml）</label>
                        <input class="field-input" type="number" id="alcohol_amount" name="alcohol_amount" placeholder="0" min="0" max="100000" required>
                    </div>
                </div>

            </div>

            <button class="input-submit" type="submit">確定</button>

        </form>

    </main>

    <!-- JS 読み込み -->
    <script>
        document.getElementById("tobacco_amount").addEventListener("invalid", (e) => {
            if (e.target.validity.valueMissing) {
                e.target.setCustomValidity("本数を入力してください");
            } else if (e.target.validity.rangeOverflow) {
                e.target.setCustomValidity("本数は100000以下で入力してください");
            }
        });
        document.getElementById("tobacco_amount").addEventListener("input", (e) => {
            e.target.setCustomValidity("");
        });

        document.getElementById("alcohol_amount").addEventListener("invalid", (e) => {
            if (e.target.validity.valueMissing) {
                e.target.setCustomValidity("量を入力してください");
            } else if (e.target.validity.rangeOverflow) {
                e.target.setCustomValidity("量は100000以下で入力してください");
            }
        });
        document.getElementById("alcohol_amount").addEventListener("input", (e) => {
            e.target.setCustomValidity("");
        });
    </script>

</body>
</html>