document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("login-form");
    loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(loginForm);
        formData.append("web_login", true);

        const res = await http.post(loginForm.getAttribute("action"), formData);
        if (res.message)
            setTimeout(() => (window.location.href = res.data), 500);
    });
});
