<?php

// PAGINATION
const TABLE_PERPAGE_NUM = 5;
const CARD_PERPAGE_NUM = 50;

// SESSION
const ACCOUNT_CURRENT_SESSION = 'ACCOUNT_CURRENT_SESSION';

// ENUM
const ADD_ENUM_TYPE = 'ADD';
const REMOVE_ENUM_TYPE = 'REMOVE';

// LOGS
const TASK_ENUM_LOG = 'TASK_ENUM_LOG';
const CONFIG_TASK_ENUM_LOG = 'CONFIG_TASK_ENUM_LOG';

const MEDIA_ENUM_LOG = 'MEDIA_ENUM_LOG';

const ERROR_ENUM_LOG = 'ERROR_ENUM_LOG';

// UPLOADS
const MEDIA_DRIVER_UPLOAD = 'MEDIA_DRIVER_UPLOAD';
const IMAGE_DRIVER_UPLOAD = 'IMAGE_DRIVER_UPLOAD';

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
    'between' => ':attribute phải nằm trong khoảng :min đến :max',
    'exists' => ':attribute không tồn tại trong hệ thống',
    'in' => ':attribute phải là một trong các giá trị: :values',
    'file.file' => 'File không hợp lệ.',
    'file.mimes' => 'File phải có định dạng: :values.',
    'file.mimetypes' => 'File phải có định dạng MIME: :values.',
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
];