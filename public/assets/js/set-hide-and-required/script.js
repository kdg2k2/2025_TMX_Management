const setHiddenAndRequired = (element, show = true, parent = null) => {
    if (!element) {
        console.warn("Element is undefined or null");
        return;
    }

    if (!show) {
        element.removeAttribute("required");
        if (parent) parent.hidden = true;
    } else {
        element.setAttribute("required", true);
        if (parent) parent.hidden = false;
    }
};
