document.addEventListener("DOMContentLoaded", () => {
    const modalLogout = document.getElementById("modal-logout");
    if (!modalLogout) return;

    const logoutForm = modalLogout.querySelector("form");

    logoutForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const res = await http.post(logoutForm.getAttribute("action"));
        if (res.message) setTimeout(() => (window.location.href = "/"), 500);
    });
});
