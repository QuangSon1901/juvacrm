$(document).ready(function () {
    $('#search-input').on('keyup', function (e) {
        if (e.key === 'Enter') {
            loadDepartments();
        }
    });

    $('#filter-active').on('change', function () {
        loadDepartments();
    });

    loadDepartments();
});

async function loadDepartments() {
    $(".table-loading").removeClass("hidden");
    const search = $('#search-input').val();
    const isActive = $('#filter-active').is(':checked') ? 1 : '';

    let method = "get",
        url = "/team/data",
        params = {
            search: search,
            is_active: isActive,
        },
        data = null;
    let res = await axiosTemplate(method, url, params, data);
    $(".table-loading").addClass("hidden");
    switch (res.data.status) {
        case 200:
            const tableBody = $('#departments-table tbody');
            let htmlContent = "";
            res.data.data.forEach(function (department) {
                htmlContent += `
                    <tr>
                        <td class="text-center">
                            <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/team/${department.id}">
                                ${department.id}
                            </a>
                        </td>
                        <td>
                            <div class="flex flex-col gap-1.5">
                                <a class="leading-none font-medium text-sm text-gray-900 hover:text-primary" href="/team/${department.id}">
                                    ${department.name}
                                </a>
                                <span class="text-2sm text-gray-700 font-normal">
                                    ${department.description}
                                </span>
                            </div>
                        </td>
                        <td class="text-sm text-gray-800 font-normal">${department.updated_at}</td>
                        <td class="text-sm text-gray-800 font-normal">${department.member_count} nhân viên</td>
                        <td>
                            <span class="badge badge-pill badge-outline ${department.status ? "badge-success" : "badge-danger"} gap-1 items-center">
                                <span class="badge badge-dot size-1.5 ${department.status ? "badge-success" : "badge-danger"}">
                                </span>
                                ${department.status ? "Đang hoạt động" : "Đã ẩn"}
                            </span>
                        </td>
                        <td>
                            <div class="menu" data-menu="true">
                                <div class="menu-item" data-menu-item-offset="0, 10px" data-menu-item-placement="bottom-end" data-menu-item-placement-rtl="bottom-start" data-menu-item-toggle="dropdown" data-menu-item-trigger="click|lg:click">
                                    <button class="menu-toggle btn btn-sm btn-icon btn-light btn-clear">
                                        <i class="ki-filled ki-dots-vertical">
                                        </i>
                                    </button>
                                    <div class="menu-dropdown menu-default w-full max-w-[175px]" data-menu-dismiss="true">
                                        <div class="menu-item">
                                            <a class="menu-link" href="/team/${department.id}">
                                                <span class="menu-icon">
                                                    <i class="ki-filled ki-search-list">
                                                    </i>
                                                </span>
                                                <span class="menu-title">
                                                    Xem chi tiết
                                                </span>
                                            </a>
                                        </div>
                                        <div class="menu-separator">
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link" href="/team/${department.id}">
                                                <span class="menu-icon">
                                                    <i class="ki-filled ki-pencil">
                                                    </i>
                                                </span>
                                                <span class="menu-title">
                                                    Chỉnh sửa
                                                </span>
                                            </a>
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link" href="#">
                                                <span class="menu-icon">
                                                    <i class="ki-filled ki-copy">
                                                    </i>
                                                </span>
                                                <span class="menu-title">
                                                    Tạo bản sao
                                                </span>
                                            </a>
                                        </div>
                                        <div class="menu-separator">
                                        </div>
                                        <div class="menu-item">
                                            <a class="menu-link" onclick="changeStatusDepartment(${department.id})">
                                                <span class="menu-icon">
                                                    <i class="ki-filled ki-shield-cross">
                                                    </i>
                                                </span>
                                                <span class="menu-title">
                                                    ${department.status ? "Ẩn" : "Mở"}
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>`;
            });

            tableBody.html(htmlContent);
            break;
        default:
            break;
    }
}

async function changeStatusDepartment(id) {
    let url = `/team/change-status/${id}`;
    let method = "post";
    let params = null;
    let data = {};

    try {
        let res = await axiosTemplate(method, url, params, data);

        switch (res.data.status) {
            case 200:
                alert(res.data.message);
                window.location.reload();
                break;
            default:
                alert("Đã có lỗi xảy ra!");
                break;
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Không thể thay đổi trạng thái. Vui lòng thử lại!");
    }
}