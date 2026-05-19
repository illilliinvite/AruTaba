// script.js
const calendar = document.getElementById("calendar");
const monthText = document.getElementById("month");

const modal = document.getElementById("modal");
const selectedDateText = document.getElementById("selected-date");
const scheduleInput = document.getElementById("schedule-input");

const saveBtn = document.getElementById("save-btn");
const deleteBtn = document.getElementById("delete-btn");
const closeBtn = document.getElementById("close-btn");

const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");

let currentDate = new Date();
let selectedDate = "";

function renderCalendar() {

  calendar.innerHTML = "";

  const year = currentDate.getFullYear();
  const month = currentDate.getMonth();

  monthText.textContent =
    `${year}年 ${month + 1}月`;

  const firstDay = new Date(year, month, 1).getDay();
  const lastDate = new Date(year, month + 1, 0).getDate();

  // 空白
  for(let i = 0; i < firstDay; i++){
    const empty = document.createElement("div");
    empty.classList.add("day");
    calendar.appendChild(empty);
  }

  // 日付
  for(let day = 1; day <= lastDate; day++){

    const dayDiv = document.createElement("div");
    dayDiv.classList.add("day");

    const today = new Date();

    if(
      year === today.getFullYear() &&
      month === today.getMonth() &&
      day === today.getDate()
    ){
      dayDiv.classList.add("today");
    }

    const dateKey =
      `${year}-${month + 1}-${day}`;

    const dayNumber =
      document.createElement("div");

    dayNumber.classList.add("day-number");
    dayNumber.textContent = day;

    const schedule =
      document.createElement("div");

    schedule.classList.add("schedule");

    schedule.textContent =
      localStorage.getItem(dateKey) || "";

    dayDiv.appendChild(dayNumber);
    dayDiv.appendChild(schedule);

    // クリック
    dayDiv.addEventListener("click", () => {

      selectedDate = dateKey;

      selectedDateText.textContent =
        `${dateKey} の予定`;

      scheduleInput.value =
        localStorage.getItem(dateKey) || "";

      modal.classList.remove("hidden");
    });

    calendar.appendChild(dayDiv);
  }
}

// 保存
saveBtn.addEventListener("click", () => {

  localStorage.setItem(
    selectedDate,
    scheduleInput.value
  );

  modal.classList.add("hidden");

  renderCalendar();
});

// 削除
deleteBtn.addEventListener("click", () => {

  localStorage.removeItem(selectedDate);

  modal.classList.add("hidden");

  renderCalendar();
});

// 閉じる
closeBtn.addEventListener("click", () => {
  modal.classList.add("hidden");
});

// 前月
prevBtn.addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  renderCalendar();
});

// 次月
nextBtn.addEventListener("click", () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  renderCalendar();
});

renderCalendar();