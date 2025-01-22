/** AXIOS TEMPLATE */
async function axiosTemplate(method, url, params, data, elements = []) {
    let x1 = moment();
    try {
        let res = await axios({
            method: method,
            url: url,
            params: params,
            data: data,
        });
        let x2 = moment();
        let time = (x2 - x1) / 1000;
        console.log("Thời gian Axios: " + time + "s");
        console.log(res);
        if (res.data.status === 308) {
            return false;
        } else if (res.data.status === 401) {
            return false;
        } else {
            return res;
        }
    } catch (e) {
        let x2 = moment();
        console.log("Kết thúc request:" + x2);
        console.log("Thời gian request:" + x2 - x1 + " ms");
        console.log(e + " AxiosTemplate" + "\n" + "url: " + url);
        if (e.response.data.status === 308) {
            return false;
        } else if (e.response.data.status === 401) {
            return false;
        }
        return e.response;
    }
}

function uploadFileTemplate(file) {
    let data = new FormData();
    data.append("file", file);
    data.append("action", 'IMAGE_DRIVER_UPLOAD');
    let method = "post",
        url = "/upload-file",
        params = null;
    return axiosTemplate(method, url, params, data);
}