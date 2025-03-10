{{-- resources/views/dashboard/contracts/tabs/info.blade.php --}}
<div class="transition-opacity duration-700" id="tab-info">
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="col-span-1">
            <div class="grid gap-6">
                {{-- Bên A (bên cung cấp) --}}
                <div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
                    <div class="card-header bg-white border-b border-gray-100">
                        <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                            <i class="ki-filled ki-crown text-blue-500"></i>
                            Bên A (bên cung cấp)
                        </h3>
                    </div>
                    <div class="card-body p-5 grid gap-4">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">Tên công ty:</span>
                            <span class="checkbox-label text-gray-800">{{NAME_COMPANY}}</span>
                        </div>
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">Mã số thuế:</span>
                            <span class="checkbox-label text-gray-800"></span>
                        </div>
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">Địa chỉ:</span>
                            <span class="checkbox-label text-gray-800"></span>
                        </div>
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">Nhân viên phụ trách:</span>
                            <a class="checkbox-label text-gray-800 hover:text-primary" href="/member/{{$details['user']['id']}}">{{$details['user']['name']}}</a>

                            @if ($canEdit)
                            <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="user_id">
                                <i class="ki-filled ki-notepad-edit"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Bên B (khách hàng) --}}
                <div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
                    <div class="card-header bg-white border-b border-gray-100">
                        <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                            <i class="ki-filled ki-user-tick text-green-500"></i>
                            Bên B (khách hàng)
                        </h3>
                    </div>
                    <div class="card-body p-5 grid gap-4">
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">Khách hàng:</span>
                            <a class="checkbox-label text-gray-800 hover:text-primary" href="/customer/{{$details['provider']['id']}}">{{$details['provider']['name']}}</a>
                            @if ($canEdit)
                            <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="provider_id">
                                <i class="ki-filled ki-notepad-edit"></i>
                            </button>
                            @endif
                        </div>
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">Người đại diện:</span>
                            <span class="checkbox-label text-gray-800">{{$details['customer_representative']}}</span>
                            @if ($canEdit)
                            <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="customer_representative">
                                <i class="ki-filled ki-notepad-edit"></i>
                            </button>
                            @endif
                        </div>
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">Mã số thuế:</span>
                            <span class="checkbox-label text-gray-800">{{$details['customer_tax_code']}}</span>
                            @if ($canEdit)
                            <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="customer_tax_code">
                                <i class="ki-filled ki-notepad-edit"></i>
                            </button>
                            @endif
                        </div>
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">Địa chỉ:</span>
                            <span class="checkbox-label text-gray-800">{{$details['address']}}</span>
                            @if ($canEdit)
                            <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="address">
                                <i class="ki-filled ki-notepad-edit"></i>
                            </button>
                            @endif
                        </div>
                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">Số điện thoại:</span>
                            <span class="checkbox-label text-gray-800">{{$details['phone']}}</span>
                            @if ($canEdit)
                            <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="phone">
                                <i class="ki-filled ki-notepad-edit"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-1 xl:!col-span-2">
            {{-- Mô tả hợp đồng --}}
            <div class="card min-w-full shadow-sm border border-gray-100 overflow-hidden">
                <div class="card-header bg-white border-b border-gray-100">
                    <h3 class="card-title font-medium text-gray-800 flex items-center gap-2">
                        <i class="ki-filled ki-document text-indigo-500"></i>
                        Mô tả hợp đồng
                    </h3>
                </div>
                <div class="card-body p-5">
                    <div class="grid gap-4">
                        <div class="flex flex-wrap gap-5">
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">Số hợp đồng:</span>
                                <span class="checkbox-label text-gray-800">{{$details['contract_number']}}</span>
                            </div>
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">Tên hợp đồng:</span>
                                <span class="checkbox-label text-gray-800">{{$details['name']}}</span>
                                @if ($canEdit)
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="name">
                                    <i class="ki-filled ki-notepad-edit"></i>
                                </button>
                                @endif
                            </div>
                        </div>

                        <div class="checkbox-group">
                            <span class="checkbox-label text-gray-800 !font-bold">Loại hình dịch vụ:</span>
                            <span class="checkbox-label text-gray-800">Chụp ảnh sản phẩm</span>
                        </div>

                        <div class="flex flex-col gap-5">
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">Ngày tạo:</span>
                                <span class="checkbox-label text-gray-800">{{formatDateTime($details['created_at'], 'd-m-Y H:i:s')}}</span>
                            </div>
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">Ngày hiệu lực:</span>
                                <span class="checkbox-label text-gray-800">{{formatDateTime($details['effective_date'], 'd-m-Y H:i:s')}}</span>
                            </div>
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">Ngày bàn giao:</span>
                                <span class="checkbox-label text-gray-800">{{formatDateTime($details['expiry_date'], 'd-m-Y H:i:s')}}</span>
                                @if ($canEdit)
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="expiry_date">
                                    <i class="ki-filled ki-notepad-edit"></i>
                                </button>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-col gap-2.5">
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">Ghi chú</span>
                                @if ($canEdit)
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="note">
                                    <i class="ki-filled ki-notepad-edit"></i>
                                </button>
                                @endif
                            </div>
                            <div class="ql-snow form-info leading-5 text-gray-800 font-normal">
                                <div class="ql-editor" style="white-space: normal;">
                                    {!! nl2br(e($details['note'] ?? '---')) !!}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2.5">
                            <div class="checkbox-group">
                                <span class="checkbox-label text-gray-800 !font-bold">Điều khoản chung</span>
                                @if ($canEdit)
                                <button class="btn btn-xs btn-icon btn-clear btn-primary" data-modal-toggle="#update-contract-modal" data-name="terms_and_conditions">
                                    <i class="ki-filled ki-notepad-edit"></i>
                                </button>
                                @endif
                            </div>
                            <div class="ql-snow form-info leading-5 text-gray-800 font-normal">
                                <div class="ql-editor" style="white-space: normal;">
                                    {!! nl2br(e($details['terms_and_conditions'] ?? '---')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>