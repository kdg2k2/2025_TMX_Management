const departmentFilter = document.getElementById("department_id");
const positionFilter = document.getElementById("position_id");
const jobTitleFilter = document.getElementById("job_title_id");
const roleFilter = document.getElementById("role_id");

window.loadList = () => {
    createDataTableServerSide(
        table,
        listUrl,
        renderColumns(),
        (item) => item,
        getFilterParams()
    );
};

const getFilterParams = () => {
    const params = {
        paginate: 1,
    };

    if (departmentFilter.value)
        params["department_id"] = departmentFilter.value;
    if (positionFilter.value) params["position_id"] = positionFilter.value;
    if (jobTitleFilter.value) params["job_title_id"] = jobTitleFilter.value;
    if (roleFilter.value) params["role_id"] = roleFilter.value;

    return params;
};

const renderColumns = () => {
    return [
        {
            data: "name",
            title: "Tên",
        },
        {
            data: null,
            title: "Avatar",
            render: (data, type, row) => {
                return `
                    <div class="lh-1">
                        <span class="avatar avatar-md avatar-rounded">
                            <img src="${row?.path}" alt="">
                        </span>
                    </div>
                `;
            },
        },
        {
            data: null,
            title: "Phòng",
            render: (data, type, row) => {
                return row?.department?.name;
            },
        },
        {
            data: null,
            title: "Chức vụ",
            render: (data, type, row) => {
                return row?.position?.name;
            },
        },
        {
            data: null,
            title: "Chức danh",
            render: (data, type, row) => {
                return row?.job_title?.name;
            },
        },
        {
            data: null,
            title: "Quyền truy cập",
            render: (data, type, row) => {
                return row?.role?.name || "";
            },
        },
        {
            data: null,
            title: "Email",
            render: (data, type, row) => {
                return `
                    <div>${row?.email}</div>
                    ${
                        row?.sub_emails?.length > 0
                            ? `<ul>${row?.sub_emails
                                  ?.map((item) => `<li>${item?.email}</li>`)
                                  .join("")}</ul>`
                            : ""
                    }
                `;
            },
        },
        {
            data: "phone",
            title: "Số điện thoại",
        },
        {
            data: "citizen_identification_number",
            title: "Số căn cước",
        },
        {
            data: null,
            title: "Khóa tài khoản",
            render: (data, type, row) => {
                if (row?.is_banned)
                    return createBadge("", "danger", "ti ti-check");
                return createBadge("", "primary", "ti ti-x");
            },
        },
        {
            data: null,
            title: "Nghỉ việc",
            render: (data, type, row) => {
                if (row?.is_retired)
                    return createBadge("", "danger", "ti ti-check");
                return createBadge("", "primary", "ti ti-x");
            },
        },
        {
            data: null,
            title: "Thời gian tạo/cập nhật",
            render: (data, type, row) => {
                return `
                    <ul class="m-0">
                        <li>${row.created_at}</li>
                        <li>${row.updated_at}</li>
                    </ul>
                `;
            },
        },
        {
            data: null,
            orderable: false,
            searchable: false,
            title: "Hành động",
            className: "text-center",
            render: (data, type, row) => {
                return `
                    ${
                        createBtn(
                            "success",
                            "Email phụ",
                            false,
                            {},
                            "ti ti-mail-code",
                            `window.location.href='${listSubEmailUrl}?user_id=${row.id}'`
                        )?.outerHTML +
                        createEditBtn(`${editUrl}?id=${row.id}`) +
                        createDeleteBtn(`${deleteUrl}?id=${row.id}`)
                    }
                `;
            },
        },
    ];
};

[departmentFilter, positionFilter, jobTitleFilter, roleFilter].forEach(
    (value, index) => {
        value.addEventListener("change", () => {
            loadList();
        });
    }
);

document.addEventListener("DOMContentLoaded", () => {
    loadList();
});
