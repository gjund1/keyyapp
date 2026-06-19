const editBtn = document.getElementById("editUsernameBtn");
const usernameText = document.getElementById("usernameText");
let isEditing = false;
let originalUsername = usernameText.textContent.trim();

editBtn.addEventListener("click", () => {
    if (isEditing) {
        usernameText.innerHTML = originalUsername;
        isEditing = false;
        return;
    }

    originalUsername = usernameText.textContent.trim();
    usernameText.innerHTML = `
        <input type="text" id="usernameInput" value="${originalUsername}" maxlength="30">
        <button id="saveUsernameBtn">OK</button>
    `;

    isEditing = true;
    const saveBtn = document.getElementById("saveUsernameBtn");

    saveBtn.addEventListener("click", async () => {
        const newName = document.getElementById("usernameInput").value.trim();

        if (!newName)
            return;

        const formData = new FormData();
        formData.append("username", newName);
        const res = await fetch("update_user.php", { method: "POST", body: formData});
        const data = await res.json();

        if (data.success) {
            usernameText.innerHTML = data.username;
            originalUsername = data.username;
            isEditing = false;
        } else {
            alert("Erreur modification");
        }
    });
});