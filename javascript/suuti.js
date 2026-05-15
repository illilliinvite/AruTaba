// 要素取得
const tabTobacco = document.getElementById("tab-tobacco");
const tabAlcohol = document.getElementById("tab-alcohol");
const amountInput = document.getElementById("amount");
const result = document.getElementById("result");
const submitBtn = document.getElementById("submit-btn");

let mode = "tobacco"; // 初期状態はタバコ

// タバコタブ
tabTobacco.addEventListener("click", () => {
    mode = "tobacco";
    tabTobacco.classList.add("active");
    tabAlcohol.classList.remove("active");
    amountInput.placeholder = "本数";
    result.textContent = "入力内容：00（○本）";
});

// アルコールタブ
tabAlcohol.addEventListener("click", () => {
    mode = "alcohol";
    tabAlcohol.classList.add("active");
    tabTobacco.classList.remove("active");
    amountInput.placeholder = "アルコール度数（%）";
    result.textContent = "入力内容：00（○ml）";
});

// 確定ボタン
submitBtn.addEventListener("click", () => {
    const brand = document.getElementById("brand").value;
    const amount = amountInput.value;

    if (mode === "tobacco") {
        result.textContent = `銘柄：${brand} ／ 本数：${amount}本`;
    } else {
        result.textContent = `銘柄：${brand} ／ アルコール度数：${amount}% ／ 飲んだ量：${(amount * 10).toFixed(0)}ml`;
    }
});
