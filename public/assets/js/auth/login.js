const redirect = (url = null) => {
    if (!url) {
        alertErr("Lỗi lấy thông tin đăng nhập");
        return;
    }

    window.location.href = url;
};

const isValidHttpUrl = (string) => {
    let url;

    try {
        url = new URL(string);
    } catch (_) {
        return false;
    }

    return url.protocol === "http:" || url.protocol === "https:";
};

const loginForm = document.getElementById("login-form");
loginForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(loginForm);
    formData.append("web_login", true);

    const res = await http.post(loginForm.getAttribute("action"), formData);
    if (res.message && res.data)
        redirect(isValidHttpUrl(res.data) ? res.data : null);
    else alertErr("Không có thông tin đăng nhập");
});
