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