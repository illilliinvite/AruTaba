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
