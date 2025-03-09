
let itemCounter = 0;
let serviceCounter = 0;
let subServiceCounter = 0;

// Thêm sản phẩm
function addProductItem() {
    const itemId = itemCounter++;
    let template = document.getElementById('product-item-template').innerHTML;
    
    // Thay thế các placeholder
    let productsOptions = '';
    if (details && details.products) {
        productsOptions = details.products.map(item => 
            `<option value="${item.id}">${item.name}</option>`
        ).join('');
    }
    
    template = template.replace(/__ITEM_ID__/g, itemId);
    template = template.replace(/__PRODUCTS_OPTIONS__/g, productsOptions);
    
    addItemToContainer(template);
}

// Thêm mục khác (giảm giá, phí khác)
function addOtherItem(type, title) {
    const itemId = itemCounter++;
    let template = document.getElementById('other-item-template').innerHTML;
    
    template = template.replace(/__ITEM_ID__/g, itemId);
    template = template.replace(/__TYPE__/g, type);
    template = template.replace(/__ITEM_TITLE__/g, title);
    
    addItemToContainer(template);
}

// Thêm item vào container
function addItemToContainer(templateHtml) {
    const container = document.getElementById('items-container');
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = templateHtml;
    container.appendChild(tempDiv.firstElementChild);
}

// Thêm dịch vụ từ danh sách
function addService(button) {
    const itemContainer = button.closest('.item-container');
    const servicesContainer = itemContainer.querySelector('.services-container');
    const serviceId = serviceCounter++;
    
    let template = document.getElementById('service-template').innerHTML;
    
    // Thay thế options
    let servicesOptions = '';
    if (details && details.services) {
        servicesOptions = details.services.map(item => 
            `<option value="${item.id}">${item.name}</option>`
        ).join('');
    }
    
    template = template.replace(/__SERVICE_ID__/g, serviceId);
    template = template.replace(/__SERVICES_OPTIONS__/g, servicesOptions);
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = template;
    servicesContainer.appendChild(tempDiv.firstElementChild);
}

// Thêm dịch vụ tùy chỉnh
function addCustomService(button) {
    const itemContainer = button.closest('.item-container');
    const servicesContainer = itemContainer.querySelector('.services-container');
    const serviceId = serviceCounter++;
    
    let template = document.getElementById('custom-service-template').innerHTML;
    template = template.replace(/__SERVICE_ID__/g, serviceId);
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = template;
    servicesContainer.appendChild(tempDiv.firstElementChild);
}

// Thêm dịch vụ con
function addSubService(button) {
    const serviceItem = button.closest('.service-item');
    const subServicesContainer = serviceItem.querySelector('.sub-services-container');
    const subServiceId = subServiceCounter++;
    
    let template = document.getElementById('sub-service-template').innerHTML;
    template = template.replace(/__SUB_SERVICE_ID__/g, subServiceId);
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = template;
    subServicesContainer.appendChild(tempDiv.firstElementChild);
}

// Xóa item (sản phẩm hoặc mục khác)
function removeItem(button) {
    const itemContainer = button.closest('.item-container');
    itemContainer.remove();
    calculateTotalValue();
}

// Xóa dịch vụ
function removeService(button) {
    const serviceItem = button.closest('.service-item');
    serviceItem.remove();
    calculateTotalValue();
}

// Xóa dịch vụ con
function removeSubService(button) {
    const subServiceItem = button.closest('.sub-service-item');
    subServiceItem.remove();
    calculateTotalValue();
}

// Kích hoạt dialog upload ảnh
function triggerImageUpload(button, type) {
    const container = button.closest('div');
    const fileInput = type === 'item' 
        ? container.querySelector('.item-image-input') 
        : container.querySelector('.sub-service-image-input');
        
    fileInput.click();
}

// Xem trước ảnh đã tải lên
function previewImage(input) {
    if (input.files && input.files[0]) {
        const container = input.closest('div');
        const previewContainer = container.querySelector('.preview-image-container');
        const previewImg = container.querySelector('img');
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Cập nhật giá dịch vụ con dựa trên đơn giá dịch vụ
function updateSubServicePrices(input) {
    const serviceItem = input.closest('.service-item');
    const subServices = serviceItem.querySelectorAll('.sub-service-item');
    
    subServices.forEach(subService => {
        calculateSubServiceTotal(subService.querySelector('.sub-service-quantity'));
    });
    
    calculateTotalValue();
}

// Tính toán thành tiền cho dịch vụ con
function calculateSubServiceTotal(input) {
    const subServiceItem = input.closest('.sub-service-item');
    const serviceItem = subServiceItem.closest('.service-item');
    const servicePrice = parseFloat(serviceItem.querySelector('.service-price').value.replace(/[.,]/g, '')) || 0;
    const quantityInput = subServiceItem.querySelector('.sub-service-quantity');
    const totalInput = subServiceItem.querySelector('.sub-service-total');
    
    const quantity = parseFloat(quantityInput.value.replace(/[.,]/g, '')) || 0;
    const total = quantity * servicePrice;
    
    // Định dạng số tiền Việt Nam
    totalInput.value = total;
    
    calculateTotalValue();
}

// Tính tổng giá trị hợp đồng
function calculateTotalValue() {
    // Tính tổng giá trị từ tất cả dịch vụ con
    let total = 0;
    let amount = 0;
    let discount = 0;
    
    // Cộng thành tiền từ tất cả dịch vụ con
    document.querySelectorAll('.sub-service-total').forEach(input => {
        const value = parseFloat(input.value.replace(/[.,]/g, '')) || 0;
        total += value;
        amount += value;
    });
    
    // Cộng/trừ giá trị từ các mục khác (giảm giá, phí...)
    document.querySelectorAll('.item-container[data-item-type="custom"]').forEach(item => {
        const priceInput = item.querySelector('input[name="item_price[]"]');
        if (priceInput) {
            const value = parseFloat(priceInput.value.replace(/[.,]/g, '')) || 0;
            total += value; // Có thể cần điều chỉnh dấu +/- tùy loại mục
            if (value < 0)
                discount += value;
            else
                amount += value;
        }
    });

    $('.services-total-value').text(formatNumberLikePhp(amount));
    $('.discount-value').text(formatNumberLikePhp(discount));
    $('.contract-total-value').text(formatNumberLikePhp(total)).val(formatNumberLikePhp(total));
    $('input[name="total_value"]').val(total);
}

// Khởi tạo với một sản phẩm mặc định
document.addEventListener('DOMContentLoaded', function() {
    addProductItem();
});