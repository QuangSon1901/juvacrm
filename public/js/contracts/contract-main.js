async function saveCreateContract() {
    calculateTotalValue();
    
    // Tạo cấu trúc dữ liệu cho sản phẩm, dịch vụ và dịch vụ con
    let contractItemsData = [];
    
    // Lặp qua tất cả các item (sản phẩm và mục khác)
    document.querySelectorAll('.item-container').forEach(itemContainer => {
        const itemId = itemContainer.dataset.itemId;
        const itemType = itemContainer.dataset.itemType;
        
        let itemData = {
            type: itemType,
            id: null,
            name: null,
            quantity: null,
            price: null,
            note: null,
            image: null,
            services: []
        };
        
        // Xử lý dựa vào loại item
        if (itemType === 'product') {
            // Sản phẩm
            itemData.id = itemContainer.querySelector('select[name="item_product_id[]"]')?.value;
            itemData.quantity = itemContainer.querySelector('input[name="item_quantity[]"]')?.value;
            
            // Lấy file ảnh nếu có
            const imageInput = itemContainer.querySelector('input[name="item_image[]"]');
            if (imageInput && imageInput.files.length > 0) {
                itemData.image = imageInput.files[0];
            }
        } else {
            // Mục khác (giảm giá, phí,...)
            itemData.name = itemContainer.querySelector('input[name="item_name[]"]')?.value;
            itemData.price = itemContainer.querySelector('input[name="item_price[]"]')?.value;
            itemData.note = itemContainer.querySelector('input[name="item_note[]"]')?.value;
        }
        
        // Thu thập dữ liệu dịch vụ cho mỗi item
        const serviceItems = itemContainer.querySelectorAll('.service-item');
        serviceItems.forEach(serviceItem => {
            const serviceId = serviceItem.dataset.serviceId;
            const isCustomService = serviceItem.querySelector('input[name="service_ids[]"][value="custom"]') !== null;
            
            let serviceData = {
                id: isCustomService ? 'custom' : serviceItem.querySelector('select[name="service_ids[]"]')?.value,
                custom_name: isCustomService ? serviceItem.querySelector('input[name="service_custom_name[]"]')?.value : null,
                price: serviceItem.querySelector('input[name="service_price[]"]')?.value,
                note: serviceItem.querySelector('input[name="service_note[]"]')?.value,
                sub_services: []
            };
            
            // Thu thập dữ liệu dịch vụ con
            const subServiceItems = serviceItem.querySelectorAll('.sub-service-item');
            subServiceItems.forEach(subServiceItem => {
                const subServiceData = {
                    name: subServiceItem.querySelector('input[name="sub_service_name[]"]')?.value,
                    quantity: subServiceItem.querySelector('input[name="sub_service_quantity[]"]')?.value,
                    total: subServiceItem.querySelector('input[name="sub_service_total[]"]')?.value,
                    content: subServiceItem.querySelector('input[name="sub_service_content[]"]')?.value,
                    image: null
                };
                
                // Lấy file ảnh dịch vụ con nếu có
                const imageInput = subServiceItem.querySelector('input[name="sub_service_image[]"]');
                if (imageInput && imageInput.files.length > 0) {
                    subServiceData.image = imageInput.files[0];
                }
                
                serviceData.sub_services.push(subServiceData);
            });
            
            itemData.services.push(serviceData);
        });
        
        contractItemsData.push(itemData);
    });
    
    // Tạo FormData để gửi dữ liệu form kèm file
    const formData = new FormData(document.getElementById('contract-form'));
    
    // Xóa các file input để tránh trùng lặp
    formData.delete('item_image[]');
    formData.delete('sub_service_image[]');
    
    // Thêm dữ liệu cấu trúc JSON
    formData.append('contract_items_data', JSON.stringify(contractItemsData));
    
    // Thêm lại các file với key phù hợp
    contractItemsData.forEach((item, itemIndex) => {
        if (item.image instanceof File) {
            formData.append(`items[${itemIndex}][image]`, item.image);
        }
        
        item.services.forEach((service, serviceIndex) => {
            service.sub_services.forEach((subService, subServiceIndex) => {
                if (subService.image instanceof File) {
                    formData.append(`items[${itemIndex}][services][${serviceIndex}][sub_services][${subServiceIndex}][image]`, subService.image);
                }
            });
        });
    });
    
    let method = "post",
        url = "/contract/create",
        params = null,
        data = formData;
    
    try {
        // Sử dụng Axios với Content-Type là multipart/form-data để gửi cả file và dữ liệu
        const axiosConfig = {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        };
        
        let res = await axios[method](url, data, axiosConfig);
        
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                // Hiệu ứng thành công trước khi tải lại trang
                setTimeout(() => {
                    window.location.href = '/contract/' + res.data.data.id;
                }, 500);
                break;
            default:
                showAlert('warning', res?.data?.message ? res.data.message : "Đã có lỗi xảy ra!");
                break;
        }
    } catch (error) {
        showAlert('error', "Đã có lỗi xảy ra khi gửi yêu cầu!");
        console.error(error);
    }
}