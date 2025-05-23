<?php

// CONFIG COMPANY
const NAME_COMPANY = "Juva Media";

// PAGINATION
const TABLE_PERPAGE_NUM = 50;
const CARD_PERPAGE_NUM = 50;

// SESSION
const ACCOUNT_CURRENT_SESSION = 'ACCOUNT_CURRENT_SESSION';

// ENUM
const ADD_ENUM_TYPE = 'ADD';
const REMOVE_ENUM_TYPE = 'REMOVE';

// LOGS
const TASK_ENUM_LOG = 'TASK_ENUM_LOG';
const PRODUCT_ENUM_LOG = 'PRODUCT_ENUM_LOG';
const CONFIG_TASK_ENUM_LOG = 'CONFIG_TASK_ENUM_LOG';

const CUSTOMER_ENUM_LOG = 'CUSTOMER_ENUM_LOG';

const MEDIA_ENUM_LOG = 'MEDIA_ENUM_LOG';

const ERROR_ENUM_LOG = 'ERROR_ENUM_LOG';

// UPLOADS
const MEDIA_DRIVER_UPLOAD = 'MEDIA_DRIVER_UPLOAD';
const IMAGE_DRIVER_UPLOAD = 'IMAGE_DRIVER_UPLOAD';
const CLOUDINARY_CLOUD_NAME = 'CLOUDINARY_CLOUD_NAME';

// VALIDATOR
const MESSAGE_VALIDATE = [
    'required' => ':attribute không được để trống',
    'string' => ':attribute phải là chuỗi ký tự',
    'integer' => ':attribute phải là số nguyên',
    'numeric' => ':attribute phải là số',
    'max' => ':attribute không được vượt quá :max ký tự',
    'min' => ':attribute phải lớn hơn hoặc bằng :min',
    'boolean' => ':attribute phải là true hoặc false',
    'date' => ':attribute phải là một ngày hợp lệ',
    'date_format' => ':attribute phải là một ngày giờ hợp lệ',
    'between' => ':attribute phải nằm trong khoảng :min đến :max',
    'exists' => ':attribute không tồn tại trong hệ thống',
    'in' => ':attribute phải là một trong các giá trị: :values',
    'file' => 'File không hợp lệ.',
    'mimes' => 'File phải có định dạng: :values.',
    'mimetypes' => 'File phải có định dạng MIME: :values.',
    'unique' => ':attribute đã tồn tại trong hệ thống.',
];

const FIELD_VALIDATE = [
    'name' => 'Tên',
    'description' => 'Mô tả',
    'note' => 'Ghi chú',
    'contract_id' => 'Hợp đồng',
    'progress' => 'Tiến độ',
    'service_id' => 'Dịch vụ',
    'service_other' => 'Dịch vụ khác',
    'priority_id' => 'Độ ưu tiên',
    'status_id' => 'Trạng thái',
    'issue_id' => 'Vấn đề',
    'estimate_time' => 'Thời gian ước tính',
    'spend_time' => 'Thời gian thực tế',
    'due_date' => 'Hạn chót',
    'parent_id' => 'Công việc cha',
    'assign_id' => 'Người được giao',
    'sub_name' => 'Tên phụ',
    'start_date' => 'Ngày bắt đầu',
    'bonus_amount' => 'Tiền thưởng',
    'deduction_amount' => 'Tiền phạt',
    'sub_task' => 'Chỉ mục',
    'type' => 'Loại',
    'sort' => 'Vị trí sắp xếp',
    'color' => 'Màu sắc',
    'is_active' => 'Trạng thái',
    'file' => 'Tệp',
    'phone' => 'Số điện thoại',
    'email' => 'Email',
    'address' => 'Địa chỉ',
    'company' => 'Công ty',
    'source_id' => 'Nguồn',
    'services' => 'Dịch vụ',
    'class_id' => 'Đối tượng',
    'status_id' => 'Trạng thái',
    'contacts' => 'Hình thức liên hệ',
    'customer_id' => 'Khách hàng',
    'message' => 'Nội dung',
    'action' => 'Hành động',
    'qty_request' => 'Số lượng yêu cầu',
    'qty_completed' => 'Số lượng đã hoàn thành',
    'contract_number' => 'Số hợp đồng',
    'user_id' => 'Nhân viên',
    'provider_id' => 'Khách hàng',
    'category_id' => 'Loại hình dịch vụ',
    'company_name' => 'Tên công ty',
    'tax_code' => 'Mã số thuế',
    'company_address' => 'Địa chỉ công ty',
    'customer_representative' => 'Người đại diện',
    'customer_tax_code' => 'Mã số thuế khách hàng',
    'sign_date' => 'Ngày ký kết',
    'effective_date' => 'Ngày hiệu lực',
    'expiry_date' => 'Ngày hết hạn',
    'estimate_day' => 'Số ngày dự kiến',
    'estimate_date' => 'Ngày dự kiến',
    'total_value' => 'Tổng giá trị',
    'terms_and_conditions' => 'Điều khoản chung',
    'quantity' => 'Số lượng',
    'price' => 'Đơn giá',
    'currency_id' => 'Loại tiền tệ',
    'status' => 'Trạng thái',
    'method_id' => 'Phương thức',
];