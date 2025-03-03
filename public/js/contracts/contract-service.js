let serviceRowCounter = 0;

function addService(type) {
    const currentIndex = serviceRowCounter++;
    let serviceRow = '';

    if (type === 'service') {
        let servicesOption = details.services.map(item => `<option value="${item.id}">${item.name}</option>`);
        serviceRow = `
            <tr class="service-row" data-type="service" data-service-id="${currentIndex}">
                <td class="!px-1">
                    <select name="service_ids[]" class="select border-gray-200 focus:border-blue-500 rounded-lg w-full service-select">
                        <option class="disabled" disabled selected>Chọn dịch vụ</option>
                        ${servicesOption}
                    </select>
                </td>
                <td class="!px-1">
                    <input name="service_quantity[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Số lượng" oninput="calculateTotalValue()">
                </td>
                <td class="!px-1">
                    <div class="relative">
                        <input name="service_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8" type="text" placeholder="Giá tiền" oninput="calculateTotalValue()">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                    </div>
                </td>
                <td class="!px-1">
                    <input name="service_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Ghi chú">
                </td>
                <td class="text-center !px-1">
                    <div class="flex items-center">
                        <button type="button" class="btn btn-sm btn-icon btn-light hover:bg-blue-100 mr-1" onclick="addSubService(this)">
                            <i class="ki-filled ki-plus-circle text-blue-500"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-light hover:bg-red-100" onclick="removeServiceWithChildren(this)">
                            <i class="ki-filled ki-trash text-red-500"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    } else {
        serviceRow = `
            <tr class="service-row" data-type="other" data-service-id="${currentIndex}">
                <td class="!px-1">
                    <input name="service_custom_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Ví dụ: Giảm giá">
                    <input type="hidden" name="service_ids[]" value="custom">
                </td>
                <td class="!px-1">
                    <input name="service_quantity[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Số lượng" oninput="calculateTotalValue()">
                </td>
                <td class="!px-1">
                    <div class="relative">
                        <input name="service_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8" type="text" placeholder="Giá tiền" oninput="calculateTotalValue()">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                    </div>
                </td>
                <td class="!px-1">
                    <input name="service_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Ghi chú">
                </td>
                <td class="text-center !px-1">
                    <div class="flex items-center">
                        <button type="button" class="btn btn-sm btn-icon btn-light hover:bg-blue-100 mr-1" onclick="addSubService(this)">
                            <i class="ki-filled ki-plus-circle text-blue-500"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-icon btn-light hover:bg-red-100" onclick="removeServiceWithChildren(this)">
                            <i class="ki-filled ki-trash text-red-500"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    $('#services-table tbody').append(serviceRow);
}

function addSubService(button) {
    const parentRow = $(button).closest('tr');
    const parentId = parentRow.data('service-id');

    const subServiceRow = `
        <tr class="sub-service-row bg-gray-50" data-parent-id="${parentId}">
            <td class="!px-1">
                <div class="flex items-center">
                    <span class="text-gray-400 mr-2">└</span>
                    <input name="sub_service_name[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Tên dịch vụ con">
                </div>
            </td>
            <td class="!px-1">
                <input name="sub_service_quantity[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Số lượng">
            </td>
            <td class="!px-1">
                <div class="relative">
                    <input name="sub_service_price[]" class="input border-gray-200 focus:border-blue-500 rounded-lg pl-8" type="text" placeholder="Giá tiền" disabled>
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">₫</span>
                </div>
            </td>
            <td class="!px-1">
                <input name="sub_service_note[]" class="input border-gray-200 focus:border-blue-500 rounded-lg" type="text" placeholder="Ghi chú">
            </td>
            <td class="text-center !px-1">
                <button type="button" class="btn btn-sm btn-icon btn-light hover:bg-red-100" onclick="$(this).closest('tr').remove();">
                    <i class="ki-filled ki-trash text-red-500"></i>
                </button>
            </td>
        </tr>
    `;

    // Find the last sub-service of this parent, or the parent itself if no sub-services
    let insertAfterRow = parentRow;
    $('#services-table tbody tr.sub-service-row').each(function() {
        if ($(this).data('parent-id') === parentId) {
            insertAfterRow = $(this);
        }
    });

    // Insert after the last related row
    insertAfterRow.after(subServiceRow);
}

function removeServiceWithChildren(button) {
    const parentRow = $(button).closest('tr');
    const parentId = parentRow.data('service-id');

    // Remove all child sub-services first
    $('#services-table tbody tr.sub-service-row').each(function() {
        if ($(this).data('parent-id') === parentId) {
            $(this).remove();
        }
    });

    // Then remove the parent row
    parentRow.remove();

    // Recalculate total after removing rows
    calculateTotalValue();
}