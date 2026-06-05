document.addEventListener("DOMContentLoaded", () => {

function loadTobaccoRanking() {

  console.log("ランキング取得開始");

  fetch("../backend/get_tobacco_ranking.php")
    .then(response => response.json())
    .then(data => {

      console.log(data);

      rankingList.innerHTML = "";

      data.forEach((user, index) => {

        let rankClass = "";

        if(index === 0){
          rankClass = "rank-1";
        }
        else if(index === 1){
          rankClass = "rank-2";
        }
        else if(index === 2){
          rankClass = "rank-3";
        }

        rankingList.innerHTML += `
          <li class="rank-item ${rankClass}">
            <span class="rank-badge">${index + 1}位</span>
            <span class="user-name">${user.user_name}</span>
            <span class="days">${user.ciggarette_consumption}本</span>
          </li>
        `;
      });

    })
    .catch(error => {
      console.error(error);
    });
}

function loadAlcoholRanking() {

  fetch("../backend/get_alcohol_ranking.php")
    .then(response => response.json())
    .then(data => {

      rankingList.innerHTML = "";

      data.forEach((user, index) => {

        let rankClass = "";

        if(index === 0){
          rankClass = "rank-1";
        }
        else if(index === 1){
          rankClass = "rank-2";
        }
        else if(index === 2){
          rankClass = "rank-3";
        }

        rankingList.innerHTML += `
          <li class="rank-item ${rankClass}">
            <span class="rank-badge">${index + 1}位</span>
            <span class="user-name">${user.user_name}</span>
            <span class="days">${user.alcohol_consumption}ml</span>
          </li>
        `;
      });

    });
}

function loadRecord() {

  fetch("../backend/get_record.php")
    .then(response => response.json())
    .then(data => {

      tobaccoBtn.dataset.days = data.tobacco_days;
      alcoholBtn.dataset.days = data.alcohol_days;

      recordDays.textContent =
        data.tobacco_days + "日";
    });
}

function checkTodayInput() {
  fetch("../backend/check_today_record.php")
    .then(response => response.json())
    .then(async data => {

      if (data.exists) return;

      const modal   = document.getElementById("guide-modal");
      const overlay = document.getElementById("guide-overlay");
      const okBtn   = document.getElementById("guide-ok-btn");
      const target  = document.getElementById("suuti-guide");

      const wait = ms => new Promise(r => setTimeout(r, ms));

      // オーバーレイ＋モーダルをふわっと表示
      overlay.style.display = "block";
      await wait(10);
      overlay.style.opacity = "1";
      modal.style.display = "block";
      await wait(10);
      modal.classList.add("show");

      okBtn.onclick = async () => {

        // モーダルを閉じる
        modal.classList.remove("show");
        await wait(400);
        modal.style.display = "none";

        // ハイライト（ブルン）
        target.classList.add("guide-highlight");
        await wait(100);
        target.classList.add("pulsing");

        target.scrollIntoView({ behavior: "smooth", block: "center" });

        // 数値入力カードをクリックしたら解除
        target.addEventListener("click", async () => {
          target.classList.remove("guide-highlight", "pulsing");
          overlay.style.opacity = "0";
          await wait(300);
          overlay.style.display = "none";
        }, { once: true });
      };
    });
}

// ボタン取得
const tobaccoBtn = document.getElementById("tab-tobacco");
const alcoholBtn = document.getElementById("tab-alcohol");

const rankingList = document.getElementById("ranking-list");

const recordLabel = document.getElementById("record-label");
const recordDays = document.getElementById("record-days");

// タバコボタン
tobaccoBtn.addEventListener("click", () => {

  loadTobaccoRanking();

  tobaccoBtn.classList.add("active");
  alcoholBtn.classList.remove("active");

  recordLabel.textContent = "禁煙継続日数";
  recordDays.textContent = tobaccoBtn.dataset.days + "日";
});

// アルコールボタン
alcoholBtn.addEventListener("click", () => {

  loadAlcoholRanking();

  alcoholBtn.classList.add("active");
  tobaccoBtn.classList.remove("active");

  recordLabel.textContent = "禁酒継続日数";
  recordDays.textContent = alcoholBtn.dataset.days + "日";
});



loadTobaccoRanking();
loadRecord();
checkTodayInput();

});