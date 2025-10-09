const getCsrfToken = () =>
    document.querySelector('meta[name="csrf-token"]').getAttribute("content");
