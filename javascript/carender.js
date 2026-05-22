// carender.js

const calendar       = document.getElementById("calendar");
const monthText      = document.getElementById("month");
const modal          = document.getElementById("modal");
const selectedDateText = document.getElementById("selected-date");
const smokeInput     = document.getElementById("smoke-input");
const alcoholInput   = document.getElementById("alcohol-input");
const saveBtn        = document.getElementById("save-btn");
const deleteBtn      = document.getElementById("delete-btn");
const closeBtn       = document.getElementById("close-btn");
const prevBtn        = document.getElementById("prev");
const nextBtn        = document.getElementById("next");

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

  renderCalendar();
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

    const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const data    = calendarData[dateKey];

    const dayDiv = document.createElement("div");
    dayDiv.classList.add("day");

    /* 今日 */
    if (
      year  === today.getFullYear() &&
      month === today.getMonth()    &&
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
      if (data.smoke   > 0) {
        const smokeLine = document.createElement("div");
        smokeLine.textContent = `🚬 ${data.smoke}本`;
        record.appendChild(smokeLine);
      }
      if (data.alcohol > 0) {
        const alcoholLine = document.createElement("div");
        alcoholLine.textContent = `🍺 ${data.alcohol}ml`;
        record.appendChild(alcoholLine);
      }
  }

    dayDiv.appendChild(dayNumber);
    dayDiv.appendChild(record);

    /* クリックでモーダルを開く */
    dayDiv.addEventListener("click", () => {

      selectedDate = dateKey;
      selectedDateText.textContent = `${year}年${month + 1}月${day}日`;

      const saved = calendarData[dateKey];
      smokeInput.value   = saved ? saved.smoke   : "";
      alcoholInput.value = saved ? saved.alcohol  : "";

      modal.classList.remove("hidden");
    });

    calendar.appendChild(dayDiv);
  }
}

/* ===== 保存 ===== */
saveBtn.addEventListener("click", async () => {

  const smoke   = parseInt(smokeInput.value)   || 0;
  const alcohol = parseInt(alcoholInput.value) || 0;

  const formData = new FormData();

  formData.append("date", selectedDate);
  formData.append("smoke", smoke);
  formData.append("alcohol", alcohol);

  const response = await fetch("../backend/save_carender.php", {
    method: "POST",
    body: formData,
    credentials: "same-origin"
  });

  const text = await response.text();

  alert(text);

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

/* ===== 前月 / 次月 ===== */
prevBtn.addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  renderCalendar();
});

nextBtn.addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  renderCalendar();
});

/* ===== 初回描画 ===== */
fetchCalendarData();