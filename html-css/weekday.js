document.addEventListener("DOMContentLoaded", () => {
    const wdSelect = document.getElementById("wdSelect");
    const wdItemInput = document.getElementById("wdItemInput");
    const wdAddBtn = document.getElementById("wdAddBtn");
    const weekdayTemplatesView = document.getElementById("weekdayTemplatesView");

    const loadWeekdayTemplates = () => {
        return JSON.parse(localStorage.getItem("weekdayTemplates") || "{}");
    };

    const saveWeekdayTemplates = (data) => {
        localStorage.setItem("weekdayTemplates", JSON.stringify(data));
    };

    const renderWeekdayTemplates = () => {
        const data = loadWeekdayTemplates();
        const order = ["月", "火", "水", "木", "金", "土", "日"];

        weekdayTemplatesView.innerHTML = "";

        order.forEach((w) => {
            const items = data[w] || [];
            const wrapper = document.createElement("div");
            wrapper.className = "weekday-block";

            const title = document.createElement("h3");
            title.textContent = `${w}曜日`;
            wrapper.appendChild(title);

            if (items.length === 0) {
                const p = document.createElement("p");
                p.textContent = "（まだ登録がありません）";
                wrapper.appendChild(p);
            } else {
                const ul = document.createElement("ul");
                items.forEach((item) => {
                    const li = document.createElement("li");
                    li.textContent = item;
                    ul.appendChild(li);
                });
                wrapper.appendChild(ul);
            }

            weekdayTemplatesView.appendChild(wrapper);
        });
    };

    wdAddBtn.addEventListener("click", () => {
        const weekday = wdSelect.value;
        const text = wdItemInput.value.trim();

        if (text === "") {
            alert("持ち物を入力してください");
            return;
        }

        const data = loadWeekdayTemplates();
        if (weekday === "毎日") {
            const all = ["月", "火", "水", "木", "金", "土", "日"];

            all.forEach(w => {
                if (!data[w]) {
                    data[w] = [];
                }
                if (!data[w].includes(text)) {
                    data[w].push(text);
                }
            });

            saveWeekdayTemplates(data);
            wdItemInput.value = "";
            renderWeekdayTemplates();
            alert(`「${text}」を月曜日〜日曜日のすべてに追加しました。`);
            return;
        }
        if (!data[weekday]) {
            data[weekday] = [];
        }

        if (!data[weekday].includes(text)) {
            data[weekday].push(text);
            saveWeekdayTemplates(data);
            wdItemInput.value = "";
            renderWeekdayTemplates();
        } else {
            alert("その持ち物はすでに登録されています。");
        }
    });

    renderWeekdayTemplates();
});
