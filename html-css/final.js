document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("tagInput");
    const saveBtn = document.getElementById("saveBtn");

    saveBtn.addEventListener("click", () => {
        let tag = input.value.trim();
        if (tag === "") return alert("入力してください");

        // 保存（localStorage を利用）
        let list = JSON.parse(localStorage.getItem("tags") || "[]");
        list.push(tag);
        localStorage.setItem("tags", JSON.stringify(list));

        alert("登録しました！");
        input.value = "";
    });
});
