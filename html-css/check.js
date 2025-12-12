document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("tagContainer");

    const getWeekday = (dateStr) => {
        if (!dateStr) return "";
        const d = new Date(dateStr);
        const weekdays = ["日", "月", "火", "水", "木", "金", "土"];
        return weekdays[d.getDay()];
    };

    // itemsByDate: { "2025-12-12": ["スマホ","教科書"], ... }
    const itemsByDate = JSON.parse(localStorage.getItem("itemsByDate") || "{}");
    const dates = Object.keys(itemsByDate).sort();

    if (dates.length === 0) {
        container.textContent = "まだ登録がありません。";
        return;
    }

    dates.forEach((dateStr) => {
        const weekday = getWeekday(dateStr);

        const title = document.createElement("h2");
        title.textContent = weekday ? `${dateStr}（${weekday}）` : dateStr;
        title.classList.add("date-title");
        container.appendChild(title);

        const ul = document.createElement("ul");
        (itemsByDate[dateStr] || []).forEach((item) => {
            const li = document.createElement("li");
            li.textContent = item;
            ul.appendChild(li);
        });

        container.appendChild(ul);
    });
});
