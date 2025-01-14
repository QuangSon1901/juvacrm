async function saveRemoveMemberTeam(user_id, department_id) {
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
            alert(res.data.message)
            window.location.reload();
            break;
        default:
            alert(res?.data?.message ? res.data.message : "Đã có lỗi xảy râ!")
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