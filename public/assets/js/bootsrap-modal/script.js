const createModal = (element) => {
    return new bootstrap.Modal(element, {
        backdrop: "static",
        keyboard: false,
    });
};

// ========== Nested Modal Z-Index Manager with Debug Logs ==========
(() => {
    const BASE_ZINDEX = 1050; // mặc định bootstrap modal z-index
    const ZINDEX_STEP = 20; // tăng mỗi modal lồng nhau
    let modalStack = []; // stack các modal đang mở (HTMLElement)

    const dbg = {
        prefix: "[NestedModal] ",
        debug(...args) {
            console.debug(this.prefix, ...args);
        },
        info(...args) {
            console.info(this.prefix, ...args);
        },
        warn(...args) {
            console.warn(this.prefix, ...args);
        },
        error(...args) {
            console.error(this.prefix, ...args);
        },
    };

    function getNumericZIndex(el) {
        try {
            const z = window.getComputedStyle(el).zIndex;
            const n = parseInt(z, 10);
            return Number.isFinite(n) ? n : BASE_ZINDEX;
        } catch (err) {
            dbg.warn("getNumericZIndex failed, fallback to BASE_ZINDEX", err);
            return BASE_ZINDEX;
        }
    }

    function findHighestOpenModalZ() {
        // Tìm modal.show trong DOM trước, fallback dùng modalStack
        const shown = Array.from(document.querySelectorAll(".modal.show"));
        if (shown.length) {
            const zs = shown.map(getNumericZIndex);
            const max = Math.max(...zs);
            dbg.debug("found shown modals z-indexes:", zs, "max:", max);
            return max;
        }
        if (modalStack.length) {
            const zs = modalStack.map(getNumericZIndex);
            const max = Math.max(...zs);
            dbg.debug("modalStack z-indexes:", zs, "max:", max);
            return max;
        }
        dbg.debug("no shown modals, using BASE_ZINDEX:", BASE_ZINDEX);
        return BASE_ZINDEX;
    }

    document.addEventListener("show.bs.modal", (event) => {
        try {
            const modalEl = event.target;
            if (!(modalEl instanceof HTMLElement)) {
                dbg.error("event.target is not HTMLElement:", modalEl);
                return;
            }

            const isNested = modalEl.getAttribute("data-nested") === "true";
            dbg.debug("isNested:", isNested);

            if (!isNested) {
                dbg.debug(
                    "Not a nested modal — skipping custom z-index/backdrop."
                );
                // ensure modalStack doesn't accidentally include non-nested modals
                return;
            }

            // compute new z-index
            const topZ = findHighestOpenModalZ();
            const newZ = topZ + ZINDEX_STEP;
            modalEl.style.zIndex = String(newZ);

            // create a unique id for the backdrop and attach it as data attr
            const bid = `nested-backdrop-${Date.now()}-${Math.floor(
                Math.random() * 10000
            )}`;
            modalEl.setAttribute("data-backdrop-id", bid);

            // create backdrop element
            const backdrop = document.createElement("div");
            backdrop.className = "modal-backdrop fade show";
            backdrop.setAttribute("data-backdrop-id", bid);
            // backdrop should be under modal -> a bit less than modal z-index
            backdrop.style.zIndex = String(newZ - 10);

            document.body.appendChild(backdrop);

            // push to stack
            modalStack.push(modalEl);
            dbg.debug(
                "modalStack after push:",
                modalStack.map((m) => m.id || m.tagName)
            );

            // ensure body has modal-open class (so scroll locked)
            if (!document.body.classList.contains("modal-open")) {
                document.body.classList.add("modal-open");
                dbg.debug("Added body.modal-open");
            }
        } catch (err) {
            dbg.error("Error in show.bs.modal handler:", err);
        }
    });

    document.addEventListener("hidden.bs.modal", (event) => {
        try {
            const modalEl = event.target;

            if (!(modalEl instanceof HTMLElement)) {
                dbg.error("event.target is not HTMLElement:", modalEl);
                return;
            }

            const bid = modalEl.getAttribute("data-backdrop-id");
            dbg.debug("backdrop-id on modal:", bid);

            if (bid) {
                const backdrop = document.querySelector(
                    `.modal-backdrop[data-backdrop-id="${bid}"]`
                );
                if (backdrop) {
                    backdrop.remove();
                } else {
                    dbg.warn("Backdrop not found for id:", bid);
                }
                modalEl.removeAttribute("data-backdrop-id");
            }

            // remove from modalStack
            const beforeLen = modalStack.length;
            modalStack = modalStack.filter((m) => m !== modalEl);
            dbg.debug(
                `modalStack length: ${beforeLen} -> ${modalStack.length}`
            );

            // if no nested modal left, remove modal-open
            if (modalStack.length === 0) {
                // but only remove if truly no modal.show in DOM
                const anyShown =
                    document.querySelectorAll(".modal.show").length > 0;
                if (!anyShown) {
                    document.body.classList.remove("modal-open");
                    dbg.debug(
                        "Removed body.modal-open (no nested modals left)"
                    );
                } else {
                    dbg.debug(
                        "Some modals still shown (non-nested?), keeping body.modal-open"
                    );
                }
            }
        } catch (err) {
            dbg.error("Error in hidden.bs.modal handler:", err);
        }
    });

    // small helper to show current internal state via console
    window.__nestedModalDebug = {
        getStack: () => modalStack.slice(),
        printState: () => {
            console.group("[NestedModal] State Dump");
            console.groupEnd();
        },
    };
})();
