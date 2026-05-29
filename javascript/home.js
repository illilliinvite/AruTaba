// ボタン取得
const tobaccoBtn = document.getElementById("tab-tobacco");
const alcoholBtn = document.getElementById("tab-alcohol");

const rankingList = document.getElementById("ranking-list");

// タバコランキング
// タバコランキング
const tobaccoRanking = `
<li class="rank-item rank-1">
  <span class="rank-badge">1位</span>
  <span class="user-name">勝原さん</span>
  <span class="days">禁煙520日</span>
</li>

<li class="rank-item rank-2">
  <span class="rank-badge">2位</span>
  <span class="user-name">佐藤さん</span>
  <span class="days">禁煙98日</span>
</li>

<li class="rank-item rank-3">
  <span class="rank-badge">3位</span>
  <span class="user-name">鈴木さん</span>
  <span class="days">禁煙76日</span>
</li>

<li class="rank-item">
  <span class="rank-badge">4位</span>
  <span class="user-name">伊藤さん</span>
  <span class="days">禁煙65日</span>
</li>

<li class="rank-item">
  <span class="rank-badge">5位</span>
  <span class="user-name">高橋さん</span>
  <span class="days">禁煙50日</span>
</li>

`;

// アルコールランキング
const alcoholRanking = `
<li class="rank-item rank-1">
  <span class="rank-badge">1位</span>
  <span class="user-name">最強さんさん</span>
  <span class="days">禁酒200日</span>
</li>

<li class="rank-item rank-2">
  <span class="rank-badge">2位</span>
  <span class="user-name">埼京線でそそり立つさん</span>
  <span class="days">禁酒180日</span>
</li>

<li class="rank-item rank-3">
  <span class="rank-badge">3位</span>
  <span class="user-name">校長さん</span>
  <span class="days">禁酒150日</span>
</li>

<li class="rank-item">
  <span class="rank-badge">4位</span>
  <span class="user-name">孫さん</span>
  <span class="days">禁酒120日</span>
</li>

<li class="rank-item">
  <span class="rank-badge">5位</span>
  <span class="user-name">半分太さん</span>
  <span class="days">禁酒100日</span>
</li>

`;

// タバコボタン
tobaccoBtn.addEventListener("click", () => {

  rankingList.innerHTML = tobaccoRanking;

  tobaccoBtn.classList.add("active");
  alcoholBtn.classList.remove("active");

});

// アルコールボタン
alcoholBtn.addEventListener("click", () => {

  rankingList.innerHTML = alcoholRanking;

  alcoholBtn.classList.add("active");
  tobaccoBtn.classList.remove("active");

});