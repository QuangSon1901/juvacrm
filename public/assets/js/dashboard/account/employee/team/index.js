async function changeStatusDepartment(id, _this) {
    let url = `/team/change-status/${id}`;
    let method = "post";
    let params = null;
    let data = {};

    try {
        let res = await axiosTemplate(method, url, params, data);

        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                callAjaxDataTable(_this.closest('.card').find('.updater'));
                break;
            default:
                showAlert('warning', "Không thể thay đổi trạng thái. Vui lòng thử lại!");
                break;
        }
    } catch (error) {
        alert("Đã có lỗi xảy ra!");
    }
}