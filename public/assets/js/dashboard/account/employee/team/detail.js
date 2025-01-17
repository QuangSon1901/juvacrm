async function saveRemoveMemberTeam(user_id, department_id, _this) {
    let method = "post",
        url = "/team/remove-member",
        params = null,
        data = {
            user_id,
            department_id
        }
    let res = await axiosTemplate(method, url, params, data);
    switch (res.data.status) {
        case 200:
            showAlert('success', res.data.message);
            callAjaxDataTable(_this.closest('.card').find('.updater'));
            break;
        default:
            showAlert('error', res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!");
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
                showAlert('success', res.data.message);
                window.location.reload();
                break;
            default:
                showAlert('warning', "Không thể thay đổi trạng thái. Vui lòng thử lại!");
                break;
        }
    } catch (error) {
        showAlert('error', "Đã có lỗi xảy ra!");
    }
}