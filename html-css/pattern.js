document.addEventListener("DOMContentLoaded", () => {
    const patternNameInput = document.getElementById("patternNameInput");
    const patternItemInput = document.getElementById("patternItemInput");
    const patternAddItemBtn = document.getElementById("patternAddItemBtn");
    const patternTemplatesView = document.getElementById("patternTemplatesView");

    const loadPatternTemplates = () => {
        return JSON.parse(localStorage.getItem("patternTemplates") || "{}");
    };

    const savePatternTemplates = (data) => {
        localStorage.setItem("patternTemplates", JSON.stringify(data));
    };

    const renderPatternTemplates = () => {
        const data = loadPatternTemplates();
        patternTemplatesView.innerHTML = "";

        Object.keys(data).forEach((name) => {
            const items = data[name] || [];

            const wrapper = document.createElement("div");
            wrapper.className = "pattern-block";

            const title = document.createElement("h3");
            title.textContent = name;
            wrapper.appendChild(title);

            if (items.length === 0) {
                const p = document.createElement("p");
                p.textContent = "（まだ持ち物がありません）";
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

            patternTemplatesView.appendChild(wrapper);
        });
    };

    patternAddItemBtn.addEventListener("click", () => {
        const name = patternNameInput.value.trim();
        const item = patternItemInput.value.trim();

        if (name === "") {
            alert("パターン名を入力してください");
            return;
        }
        if (item === "") {
            alert("持ち物を入力してください");
            return;
        }

        const data = loadPatternTemplates();
        if (!data[name]) {
            data[name] = [];
        }

        if (!data[name].includes(item)) {
            data[name].push(item);
            savePatternTemplates(data);
            patternItemInput.value = "";
            renderPatternTemplates();
        } else {
            alert("その持ち物はすでにこのパターンに登録されています。");
        }
    });

    renderPatternTemplates();
});
