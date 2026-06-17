// carender.js

const calendar       = document.getElementById("calendar");
const monthText      = document.getElementById("month");
const modal          = document.getElementById("modal");
const selectedDateText = document.getElementById("selected-date");
const smokeInput     = document.getElementById("smoke-input");
const alcoholInput   = document.getElementById("alcohol-input");
const brandInput     = document.getElementById("brand-input");
const degreeInput    = document.getElementById("degree-input");
const saveBtn        = document.getElementById("save-btn");
const deleteBtn      = document.getElementById("delete-btn");
const closeBtn       = document.getElementById("close-btn");
const prevBtn        = document.getElementById("prev");
const nextBtn        = document.getElementById("next");

const warningModal    = document.getElementById("warning-modal");
const warningMessage  = document.getElementById("warning-message");
const warningLevelBadge = document.getElementById("warning-level-badge");
const warningGaugeArc    = document.getElementById("warning-gauge-arc");
const breakdownBarSmoke  = document.getElementById("breakdown-bar-smoke");
const breakdownBarAlcohol = document.getElementById("breakdown-bar-alcohol");
const breakdownSmokePercent  = document.getElementById("breakdown-smoke-percent");
const breakdownAlcoholPercent = document.getElementById("breakdown-alcohol-percent");
const warningImage    = document.getElementById("warning-image");
const warningImage2   = document.getElementById("warning-image2");
const warningVideo    = document.getElementById("warning-video");
const warningVideoWrap = document.getElementById("warning-video-wrap");
const warningCloseBtn = document.getElementById("warning-close-btn");

warningModal.style.display = "none";

let currentDate  = new Date();
let selectedDate = "";
let calendarData = {};

async function fetchCalendarData() {

  const response = await fetch(
    "../backend/get_carender.php",
    {
      credentials: "same-origin"
    }
  );

  calendarData = await response.json();

  console.log(calendarData);

  renderCalendar();

  checkMonthlyScore();
}

/* ===== 月間スコア判定 ===== */
/* ===== 警告レベルのしきい値 ===== */
const SCORE_LEVEL_CAUTION = 30000; // 注意
const SCORE_LEVEL_WARNING = 50000; // 警告
const SCORE_LEVEL_DANGER  = 80000; // 危険

function checkMonthlyScore() {

  const year  = currentDate.getFullYear();
  const month = currentDate.getMonth();

  const currentMonthPrefix =
    `${year}-${String(month + 1).padStart(2, '0')}`;

  let totalScore   = 0;
  let smokeScore   = 0;
  let alcoholScore = 0;

  for (const date in calendarData) {

    if (date.startsWith(currentMonthPrefix)) {

      const d = calendarData[date];

      const sScore = (Number(d.smoke) || 0) * 400;
      const aScore = (Number(d.alcohol) || 0) * (Number(d.degree) || 1);

      smokeScore   += sScore;
      alcoholScore += aScore;
      totalScore   += Number(d.score) || 0;
    }
  }

  if (totalScore < SCORE_LEVEL_CAUTION) {
    return; // 基準未満なら警告なし
  }

  /* ----- レベル判定 ----- */
  let levelKey, levelLabel, levelColor;

  if (totalScore >= SCORE_LEVEL_DANGER) {
    levelKey   = "danger";
    levelLabel = "危険";
    levelColor = "#d32f2f";
  } else if (totalScore >= SCORE_LEVEL_WARNING) {
    levelKey   = "warning";
    levelLabel = "警告";
    levelColor = "#f57c00";
  } else {
    levelKey   = "caution";
    levelLabel = "注意";
    levelColor = "#fbc02d";
  }

  /* ----- 内訳判定 ----- */
  const smokeRatio = totalScore > 0
    ? Math.round((smokeScore / totalScore) * 100)
    : 0;

  const alcoholRatio = 100 - smokeRatio;

  let mainCause;

  if (smokeRatio >= 60) {
    mainCause = "特にタバコの量が多くなっています。";
  } else if (alcoholRatio >= 60) {
    mainCause = "特にお酒の量が多くなっています。";
  } else {
    mainCause = "タバコ・お酒どちらも増えています。";
  }

  /* ----- レベルごとの本文 ----- */
  let bodyMessage;

  if (levelKey === "danger") {
    bodyMessage = "このままの生活を続けると、深刻な健康被害につながる可能性が高い状態です。早めの見直しを強くおすすめします。";
  } else if (levelKey === "warning") {
    bodyMessage = "肺や血管への負担が大きくなる可能性があります。生活習慣を見直すタイミングです。";
  } else {
    bodyMessage = "今のペースが続くと負担が大きくなる可能性があります。少し意識してみましょう。";
  }

  /* ----- 表示反映 ----- */
  warningLevelBadge.textContent = `${levelLabel}`;
  warningLevelBadge.style.background = levelColor;
  warningLevelBadge.style.color = "#fff";
  warningLevelBadge.style.padding = "4px 10px";
  warningLevelBadge.style.borderRadius = "12px";
  warningLevelBadge.style.fontSize = "12px";

  // ゲージの円弧（円周 = 2πr ≒ 314、スコアに応じて0〜100%を描画。100000で満タン扱い）
  const gaugeRatio = Math.min(totalScore / 100000, 1);
  const circumference = 2 * Math.PI * 50; // r=50
  warningGaugeArc.style.stroke = levelColor;
  warningGaugeArc.setAttribute(
    "stroke-dasharray",
    `${circumference * gaugeRatio} ${circumference}`
  );

  // 内訳バー
  breakdownBarSmoke.style.width   = `${smokeRatio}%`;
  breakdownBarAlcohol.style.width = `${alcoholRatio}%`;
  breakdownSmokePercent.textContent   = `${smokeRatio}%`;
  breakdownAlcoholPercent.textContent = `${alcoholRatio}%`;

  warningMessage.textContent =
    `今月の喫煙・飲酒スコアが${levelLabel}基準に達しました。${mainCause}${bodyMessage}`;

  warningImage.src  = "../image/warning_smoke.jpg";
  warningImage2.src = "../image/warning_alcohol.jpg";

  // 動画ID（後で差し替えやすいようにまとめておく）
  const VIDEO_ID_SMOKE   = "7z88JgOQoKY"; // タバコ用
  const VIDEO_ID_ALCOHOL = "0GIZIhurBYE"; // アルコール用

  const videoId = (smokeRatio >= 60) ? VIDEO_ID_SMOKE : VIDEO_ID_ALCOHOL;

  if (videoId) {
    warningVideoWrap.style.display = "block";
    warningVideo.src = `https://www.youtube.com/embed/${videoId}`;
  } else {
    warningVideoWrap.style.display = "none";
    warningVideo.src = "";
  }

  warningModal.style.display = "flex";
}

/* ===== カレンダー描画 ===== */
function renderCalendar() {

  calendar.innerHTML = "";

  const year  = currentDate.getFullYear();
  const month = currentDate.getMonth();

  monthText.textContent = `${year}年 ${month + 1}月`;

  const firstDay = new Date(year, month, 1).getDay();
  const lastDate  = new Date(year, month + 1, 0).getDate();
  const today     = new Date();

  /* 空白マス */
  for (let i = 0; i < firstDay; i++) {

    const empty = document.createElement("div");

    empty.classList.add("day");

    calendar.appendChild(empty);
  }

  /* 日付マス */
  for (let day = 1; day <= lastDate; day++) {

    const dateKey =
      `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

    const data = calendarData[dateKey];

    const dayDiv = document.createElement("div");

    dayDiv.classList.add("day");

    /* 今日 */
    if (
      year  === today.getFullYear() &&
      month === today.getMonth() &&
      day   === today.getDate()
    ) {
      dayDiv.classList.add("today");
    }

    /* 日付番号 */
    const dayNumber = document.createElement("div");

    dayNumber.classList.add("day-number");

    dayNumber.textContent = day;

    /* 記録表示 */
    const record = document.createElement("div");

    record.classList.add("day-record");

    if (data) {

      const smokeLine = document.createElement("div");
      smokeLine.textContent = data.brand
          ? `🚬 ${data.smoke}本（${data.brand}）`
          : `🚬 ${data.smoke}本`;
      record.appendChild(smokeLine);

      const alcoholLine = document.createElement("div");
      alcoholLine.textContent = data.degree
          ? `🍺 ${data.alcohol}ml（${data.degree}%）`
          : `🍺 ${data.alcohol}ml`;
      record.appendChild(alcoholLine);
    }

    dayDiv.appendChild(dayNumber);
    dayDiv.appendChild(record);

    /* クリックでモーダル */
    dayDiv.addEventListener("click", () => {

      selectedDate = dateKey;

      selectedDateText.textContent =
        `${year}年${month + 1}月${day}日`;

      const saved = calendarData[dateKey];

      smokeInput.value =
          saved ? saved.smoke : "";

      alcoholInput.value =
          saved ? saved.alcohol : "";

      brandInput.value =
          saved ? (saved.brand ?? "") : "";

      degreeInput.value =
          saved ? (saved.degree ?? "") : "";

      modal.classList.remove("hidden");
    });

    calendar.appendChild(dayDiv);
  }
}

/* ===== 保存 ===== */
saveBtn.addEventListener("click", async () => {

  const smoke   = parseInt(smokeInput.value)   || 0;
  const alcohol = parseInt(alcoholInput.value) || 0;
  const brand   = brandInput.value;
  const degree  = degreeInput.value;

  const formData = new FormData();

  formData.append("date",    selectedDate);
  formData.append("smoke",   smoke);
  formData.append("alcohol", alcohol);
  formData.append("brand",   brand);
  formData.append("degree",  degree);

  await fetch("../backend/save_carender.php", {
    method: "POST",
    body: formData,
    credentials: "same-origin"
  });

  modal.classList.add("hidden");

  fetchCalendarData();
});

/* ===== 削除 ===== */
deleteBtn.addEventListener("click", async () => {

  const formData = new FormData();

  formData.append("date", selectedDate);

  await fetch("../backend/delete_carender.php", {
    method: "POST",
    body: formData,
    credentials: "same-origin"
  });

  modal.classList.add("hidden");

  fetchCalendarData();
});

/* ===== 閉じる ===== */
closeBtn.addEventListener("click", () => {

  modal.classList.add("hidden");
});

/* ===== 警告閉じる ===== */
warningCloseBtn.addEventListener("click", () => {

  warningModal.style.display = "none";
  warningVideo.src = "";
});

/* ===== 前月 / 次月 ===== */
prevBtn.addEventListener("click", () => {

  currentDate.setMonth(currentDate.getMonth() - 1);

  renderCalendar();
});

nextBtn.addEventListener("click", () => {

  currentDate.setMonth(currentDate.getMonth() + 1);

  renderCalendar();
});

/* ===== 警告モーダル タブ切り替え ===== */
document.querySelectorAll(".warning-tab").forEach((tab) => {

  tab.addEventListener("click", () => {

    document.querySelectorAll(".warning-tab").forEach((t) => t.classList.remove("active"));
    document.querySelectorAll(".warning-tab-panel").forEach((p) => p.classList.add("hidden"));

    tab.classList.add("active");

    const panel = document.querySelector(`.warning-tab-panel[data-panel="${tab.dataset.tab}"]`);
    panel.classList.remove("hidden");
  });
});

/* ===== 初回描画 ===== */
fetchCalendarData();