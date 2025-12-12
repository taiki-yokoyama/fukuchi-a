document.addEventListener("DOMContentLoaded", () => {
    const dateInput = document.getElementById("dateInput");
    const weekdayLabel = document.getElementById("weekdayLabel");

    const existingSection = document.getElementById("existingSection");
    const existingList = document.getElementById("existingList");

    const manualSection = document.getElementById("manualSection");
    const itemInput = document.getElementById("itemInput");
    const addItemBtn = document.getElementById("addItemBtn");

    const noDataSection = document.getElementById("noDataSection");
    const useWeekdayBtn = document.getElementById("useWeekdayBtn");
    const usePatternBtn = document.getElementById("usePatternBtn");
    const useCustomBtn = document.getElementById("useCustomBtn");

    const patternSelectSection = document.getElementById("patternSelectSection");
    const patternSelect = document.getElementById("patternSelect");
    const applyPatternBtn = document.getElementById("applyPatternBtn");

    // ====== ヘルパー関数たち ======
    const getWeekday = (dateStr) => {
        if (!dateStr) return "";
        const d = new Date(dateStr);
        const weekdays = ["日", "月", "火", "水", "木", "金", "土"];
        return weekdays[d.getDay()];
    };

    const loadItemsByDate = () => {
        return JSON.parse(localStorage.getItem("itemsByDate") || "{}");
    };

    const saveItemsByDate = (data) => {
        localStorage.setItem("itemsByDate", JSON.stringify(data));
    };

    const loadWeekdayTemplates = () => {
        return JSON.parse(localStorage.getItem("weekdayTemplates") || "{}");
    };

    const loadPatternTemplates = () => {
        return JSON.parse(localStorage.getItem("patternTemplates") || "{}");
    };

    const renderExistingList = (items) => {
        existingList.innerHTML = "";
        items.forEach((item) => {
            const li = document.createElement("li");
            li.textContent = item;
            existingList.appendChild(li);
        });
    };

    const updateViewForDate = (dateStr) => {
        const weekday = getWeekday(dateStr);
        weekdayLabel.textContent = weekday ? `（${weekday}）` : "";

        const itemsByDate = loadItemsByDate();
        const items = itemsByDate[dateStr] || [];

        if (items.length > 0) {
            // すでに登録がある日
            existingSection.style.display = "block";
            manualSection.style.display = "flex";
            noDataSection.style.display = "none";
            patternSelectSection.style.display = "none";

            renderExistingList(items);
        } else {
            // まだ何も登録していない日
            existingSection.style.display = "none";
            manualSection.style.display = "none"; // 「自分で登録」を選んだら表示
            noDataSection.style.display = "block";
            patternSelectSection.style.display = "none";
            existingList.innerHTML = "";
        }
    };

    // ====== 初期化 ======
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, "0");
    const dd = String(today.getDate()).padStart(2, "0");
    const todayStr = `${yyyy}-${mm}-${dd}`;
    dateInput.value = todayStr;

    updateViewForDate(todayStr);

    // ====== イベント設定 ======

    // 日付変更時
    dateInput.addEventListener("change", () => {
        updateViewForDate(dateInput.value);
    });

    // 手動でアイテム追加（どの日でも使える）
    addItemBtn.addEventListener("click", () => {
        const text = itemInput.value.trim();
        const dateStr = dateInput.value;

        if (dateStr === "") {
            alert("日付を選んでください");
            return;
        }
        if (text === "") {
            alert("持ち物を入力してください");
            return;
        }

        const itemsByDate = loadItemsByDate();
        if (!itemsByDate[dateStr]) {
            itemsByDate[dateStr] = [];
        }
        itemsByDate[dateStr].push(text);
        saveItemsByDate(itemsByDate);

        itemInput.value = "";
        updateViewForDate(dateStr);
    });

    // 「自分で登録」ボタン → 追加欄を表示
    useCustomBtn.addEventListener("click", () => {
        manualSection.style.display = "flex";
    });

    // 「曜日で登録」ボタン
    useWeekdayBtn.addEventListener("click", () => {
        const dateStr = dateInput.value;
        if (!dateStr) {
            alert("日付を選んでください");
            return;
        }
        const weekday = getWeekday(dateStr); // "月" など
        const weekdayTemplates = loadWeekdayTemplates();
        const items = weekdayTemplates[weekday];

        if (!items || items.length === 0) {
            alert("この曜日の持ち物テンプレがまだありません。設定ページで登録してください。");
            window.location.href = "weekday.html";
            return;
        }

        const itemsByDate = loadItemsByDate();
        itemsByDate[dateStr] = [...items]; // テンプレからコピー
        saveItemsByDate(itemsByDate);

        updateViewForDate(dateStr);
        alert(`${weekday}曜日のテンプレから登録しました。`);
    });

    // 「パターンで登録」ボタン
    usePatternBtn.addEventListener("click", () => {
        const patterns = loadPatternTemplates();
        const names = Object.keys(patterns);

        if (names.length === 0) {
            alert("まだパターンがありません。パターン設定ページで登録してください。");
            window.location.href = "pattern.html";
            return;
        }

        // セレクトボックスにパターン名をセット
        patternSelect.innerHTML = "";
        names.forEach((name) => {
            const option = document.createElement("option");
            option.value = name;
            option.textContent = name;
            patternSelect.appendChild(option);
        });

        patternSelectSection.style.display = "block";
    });

    // 「このパターンで登録」ボタン
    applyPatternBtn.addEventListener("click", () => {
        const dateStr = dateInput.value;
        if (!dateStr) {
            alert("日付を選んでください");
            return;
        }

        const patterns = loadPatternTemplates();
        const name = patternSelect.value;
        const items = patterns[name] || [];

        if (items.length === 0) {
            alert("このパターンにはまだ持ち物がありません。");
            return;
        }

        const itemsByDate = loadItemsByDate();
        itemsByDate[dateStr] = [...items];
        saveItemsByDate(itemsByDate);

        patternSelectSection.style.display = "none";
        updateViewForDate(dateStr);
        alert(`パターン「${name}」から登録しました。`);
    });
});
