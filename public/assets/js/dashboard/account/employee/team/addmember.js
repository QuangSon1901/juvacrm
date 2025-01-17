$(document).ready(function () {
    $('#create-level-modal').on('submit', function (e) {
        e.preventDefault();

        let levelName = $('#create-level-modal input[type="text"]').val().trim();

        if (!levelName) {
            showAlert('warning', 'Vui lòng nhập tên chức vụ!');
            return;
        }

        $('table select[name="level"]').each(function () {
            $(this).append(`<option value="-1">${levelName}</option>`);
        });

        levels.push({id: -1, name: levelName})

        $('#create-level-modal input[type="text"]').val('');
        $('#create-level-modal .btn-close').click();
    });
});

function addRowLevelTable() {
    let usersHTML = users.map(item => `<option value=${item.id}>${item.name}</option>`).join("");
    let levelsHTML = levels.map(item => `<option value=${item.id}>${item.name}</option>`).join("");

    let rowHTMl = `<tr>
                        <td>
                            <select class="select" name="employee">
                                <option selected disabled>
                                    Chọn nhân viên
                                </option>
                                ${usersHTML}
                            </select>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <select class="select" name="level">
                                    <option selected disabled>
                                        Chọn chức vụ
                                    </option>
                                    ${levelsHTML}
                                </select>
                                <button class="input !w-max" data-modal-toggle="#create-level-modal">
                                    Thêm
                                </button>
                            </div>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-icon btn-light btn-clear" onclick="removeRowLevelTable($(this))">
                                <i class="ki-filled ki-trash !text-red-600">
                                </i>
                            </button>
                        </td>
                    </tr>`;
    $("#members-table tbody").append(rowHTMl);
}

function removeRowLevelTable(_this) {
    _this.closest("tr").remove();
}

async function saveAddMemberTeam(id) {
    let users = [];
    try {
        $('#members-table tbody tr').each(function () {
            let id = $(this).find('select[name="employee"]').val().trim();
            let level = $(this).find('select[name="level"] :selected').val().trim();
            let name = $(this).find('select[name="level"] :selected').text().trim();
    
            if (id && name) {
                users.push({ id: parseInt(id), level: {id: level, name} });
            }
        });
    } catch (error) {
        showAlert('warning', "Vui lòng chọn thông tin nhân viên");
        return;
    }
    let method = "post",
        url = "/team/add-member",
        params = null,
        data = {
            id,
            users
        }
    let res = await axiosTemplate(method, url, params, data);
    switch (res.data.status) {
        case 200:
            showAlert('success', res.data.message)
            window.location.href='/team/' + id;
            break;
        default:
            showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
            break;
    }
}