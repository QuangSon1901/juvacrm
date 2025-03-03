// public/js/contracts/contract-main.js
async function saveCreateContract() {
    calculateTotalValue();
    
    // Create structured data for services and sub-services
    let servicesData = [];
    
    $('#services-table tbody tr.service-row').each(function() {
        const row = $(this);
        const type = row.data('type');
        const serviceId = row.data('service-id');
        
        let serviceData = {
            type: type,
            id: type === 'service' ? row.find('select[name="service_ids[]"]').val() : 'custom',
            custom_name: type === 'other' ? row.find('input[name="service_custom_name[]"]').val() : null,
            quantity: row.find('input[name="service_quantity[]"]').val(),
            price: row.find('input[name="service_price[]"]').val(),
            note: row.find('input[name="service_note[]"]').val(),
            sub_services: []
        };
        
        // Find child sub-services
        $('#services-table tbody tr.sub-service-row').each(function() {
            const subRow = $(this);
            if (subRow.data('parent-id') === serviceId) {
                serviceData.sub_services.push({
                    name: subRow.find('input[name="sub_service_name[]"]').val(),
                    quantity: subRow.find('input[name="sub_service_quantity[]"]').val(),
                    note: subRow.find('input[name="sub_service_note[]"]').val()
                });
            }
        });
        
        servicesData.push(serviceData);
    });
    
    // Add services data to form data
    let formData = $('#contract-form').serialize();
    formData += '&services_data=' + encodeURIComponent(JSON.stringify(servicesData));

    let method = "post",
        url = "/contract/create",
        params = null,
        data = formData;
    
    try {
        let res = await axiosTemplate(method, url, params, data);
        switch (res.data.status) {
            case 200:
                showAlert('success', res.data.message);
                // Success effect before page reload
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
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

function calculateTotalValue() {
    let total = 0;
    $('#services-table tbody tr.service-row').each(function() {
        const quantity = parseFloat($(this).find('input[name="service_quantity[]"]').val()) || 0;
        const price = parseFloat($(this).find('input[name="service_price[]"]').val()) || 0;
        total += quantity * price;
    });
    $('input[name="total_value"]').val(total);
}