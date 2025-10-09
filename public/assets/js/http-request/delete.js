document.addEventListener("DOMContentLoaded", () => {
    const modalDelete = $("#modalDelete");
    modalDelete.on("show.bs.modal", (e) => {
        const btnDelete = modalDelete.find("button[type='submit']");
        const trigger = $(e.relatedTarget);
        const deleteUrl = trigger.data("href");
        const onSuccessFnName = trigger.data("onsuccess");

        btnDelete.off("click").on("click", async function (evt) {
            evt.preventDefault();

            const res = await http.delete(deleteUrl);
            modalDelete.modal("hide");
            if (
                res.message &&
                onSuccessFnName &&
                typeof window[onSuccessFnName] === "function"
            )
                window[onSuccessFnName]();
        });
    });
});
