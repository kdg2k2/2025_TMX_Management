"use strict";

// for show password
let createpassword = (type, ele) => {
    document.getElementById(type).type =
        document.getElementById(type).type == "password" ? "text" : "password";
    const iconContainer = ele.querySelector("i");
    let icon = iconContainer.classList;
    let stringIcon = icon.toString();

    if (stringIcon.includes("ri-eye-line")) {
        iconContainer.classList.remove("ri-eye-line");
        iconContainer.classList.add("ri-eye-off-line");
    } else {
        iconContainer.classList.add("ri-eye-line");
        iconContainer.classList.remove("ri-eye-off-line");
    }
};
