const makeNotifly = (style, mess, delay) => {
    // https://github.com/91ahmed/nostfly
    new Nostfly({
        style: style, // 'warning','success','error','attention','notify','note'
        position: "top-center", // 'top-right','top-left','top-center','bottom-right','bottom-left','bottom-center'
        closeAnimate: "nostfly-close-slide-up", // 'nostfly-close-slide-right','nostfly-close-slide-left','nostfly-close-slide-up','nostfly-close-slide-down','nostfly-close-fade'
        openAnimate: "nostfly-open-slide-down", // 'nostfly-open-slide-right','nostfly-open-slide-left','nostfly-open-slide-up','nostfly-open-slide-down','nostfly-open-fade'
        loaderPosition: "top",
        iconHeader: true,
        content: mess,
        auto: true,
        loader: true,
        delay: delay,
    });
};
const alertSuccess = (mess = null, delay = 5000) => {
    makeNotifly("success", mess, delay);
};
const alertErr = (mess = null, delay = 5000) => {
    makeNotifly("error", mess, delay);
};
const alertInfo = (mess = null, delay = 5000) => {
    makeNotifly("notify", mess, delay);
};
const alertWarning = (mess = null, delay = 5000) => {
    makeNotifly("warning", mess, delay);
};
