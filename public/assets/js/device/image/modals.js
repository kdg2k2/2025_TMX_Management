// Tắt auto discover của Dropzone
Dropzone.autoDiscover = false;
var storeDropzone, updateDropzone;
const modalStore = document.getElementById("modal-store");
const modalStoreForm = modalStore.querySelector("form");
const modalUpdate = document.getElementById("modal-update");
const modalUpdateForm = modalUpdate.querySelector("form");

// Khai báo hàm openStoreModal ở scope global
const openStoreModal = (btn) => {
    btn.dataset.href = `${storeUrl}?device_id=${deviceId}`;
    openModalBase(btn, {
        modal: modalStore,
        form: modalStoreForm,
    });
};

const openUpdateModal = (btn) => {
    openModalBase(btn, {
        modal: modalUpdate,
        form: modalUpdateForm,
    });
};

modalStoreForm.addEventListener("submit", (e) => {
    e.preventDefault();
    if (storeDropzone.getQueuedFiles().length > 0) {
        storeDropzone.processQueue();
    } else {
        alertErr("Vui lòng chọn ít nhất 1 ảnh");
    }
});

modalUpdateForm.addEventListener("submit", (e) => {
    e.preventDefault();
    if (updateDropzone.getQueuedFiles().length > 0) {
        updateDropzone.processQueue();
    } else {
        alertErr("Vui lòng chọn ảnh để cập nhật");
    }
});

document.addEventListener("DOMContentLoaded", () => {
    // Store Dropzone - Upload nhiều ảnh
    storeDropzone = new Dropzone("#store-dropzone", {
        url: () => modalStoreForm.getAttribute("action"),
        paramName: "path",
        chunking: false,
        uploadMultiple: true,
        parallelUploads: 10,
        maxFiles: 10,
        maxFilesize: 5,
        acceptedFiles: ".png,.jpg,.jpeg,.webp",
        autoProcessQueue: false,
        addRemoveLinks: true,
        dictRemoveFile: "Xóa",
        dictCancelUpload: "Hủy",
        dictMaxFilesExceeded: "Chỉ được chọn tối đa 10 ảnh",
        headers: {
            "X-CSRF-TOKEN": getCsrfToken(),
        },
        init: function () {
            this.on("addedfile", function (file) {
            });

            this.on("sendingmultiple", function (files, xhr, formData) {
                formData.append("device_id", deviceId);
            });

            this.on("successmultiple", function (files, response) {
                if (response.message) {
                    alertSuccess(response.message);
                    this.removeAllFiles();
                    hideModal(modalStore);
                    loadList();
                }
            });

            this.on("errormultiple", function (files, errorMessage) {
                console.error("Error:", errorMessage);
                alertErr(
                    "Upload thất bại: " +
                        (errorMessage.message || JSON.stringify(errorMessage))
                );
                this.removeAllFiles();
            });

            this.on("maxfilesexceeded", function (file) {
                alertErr("Chỉ được chọn tối đa 10 ảnh");
                this.removeFile(file);
            });
        },
    });

    // Update Dropzone - Chỉ upload 1 ảnh
    updateDropzone = new Dropzone("#update-dropzone", {
        url: () => modalUpdateForm.getAttribute("action"),
        method: "POST",
        paramName: "path",
        chunking: false,
        maxFiles: 1,
        maxFilesize: 5,
        acceptedFiles: ".png,.jpg,.jpeg,.webp",
        autoProcessQueue: false,
        addRemoveLinks: true,
        dictRemoveFile: "Xóa",
        dictCancelUpload: "Hủy",
        headers: {
            "X-CSRF-TOKEN": getCsrfToken(),
        },
        init: function () {
            this.on("addedfile", function (file) {
                if (this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
            });

            this.on("sending", function (file, xhr, formData) {
                formData.append("_method", "PATCH");
            });

            this.on("success", function (file, response) {
                if (response.message) {
                    alertSuccess(response.message);
                    this.removeAllFiles();
                    hideModal(modalUpdate);
                    loadList();
                }
            });

            this.on("error", function (file, errorMessage) {
                console.error("Error:", errorMessage);
                alertErr(
                    "Upload thất bại: " +
                        (errorMessage.message || JSON.stringify(errorMessage))
                );
                this.removeAllFiles();
            });

            this.on("maxfilesexceeded", function (file) {
                alertErr("Chỉ được chọn 1 ảnh");
                this.removeFile(file);
            });
        },
    });
});
