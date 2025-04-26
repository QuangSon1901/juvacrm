@extends('dashboard.layouts.layout')
@section('dashboard_content')
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Phân quyền cho chức vụ {{$details['level']->name}} - {{$details['department']->name}}
            </h1>
        </div>
    </div>
</div>
<div class="container-fixed">
    <div class="grid gap-5">
        <!-- Quick actions buttons -->
        <div class="flex justify-end gap-3">
            <button class="btn btn-sm btn-light" id="select-all-permissions">
                <i class="ki-duotone ki-check-circle fs-2"></i> Chọn tất cả
            </button>
            <button class="btn btn-sm btn-light" id="unselect-all-permissions">
                <i class="ki-duotone ki-cross-circle fs-2"></i> Bỏ chọn tất cả
            </button>
        </div>

        <form id="permissions-form">
            <!-- Overall progress -->
            <div class="card mb-5">
                <div class="card-body">
                    <h3 class="card-title mb-3">Tổng quan phân quyền</h3>
                    <div class="progress h-5px mb-3">
                        <div class="progress-bar bg-primary" role="progressbar" id="permission-progress-bar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fs-7">Số quyền đã chọn: <span id="selected-count">0</span>/<span id="total-count">0</span></span>
                        <span class="text-muted fs-7">Tỷ lệ: <span id="selected-percentage">0%</span></span>
                    </div>
                </div>
            </div>

            @foreach ($permissions as $module => $modulePermissions)
            <div class="card mb-5">
                <div class="card-header d-flex align-items-center">
                    <h3 class="card-title flex-grow-1">{{ $module }}</h3>
                    <div>
                        <button type="button" class="btn btn-sm btn-light select-module" data-module="{{ $module }}">
                            <i class="ki-duotone ki-check fs-2"></i> Chọn module này
                        </button>
                        <button type="button" class="btn btn-sm btn-light unselect-module" data-module="{{ $module }}">
                            <i class="ki-duotone ki-cross fs-2"></i> Bỏ chọn
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- CRUD permissions -->
                        <div class="col-md-6">
                            <h4 class="mb-3 fs-6 fw-semibold">Quyền cơ bản</h4>
                            <div class="d-flex flex-column gap-3">
                                @if($viewPerm = $modulePermissions->firstWhere('slug', 'like', 'view-' . strtolower($module)))
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input module-{{ $module }}" type="checkbox" 
                                           name="permissions[]" value="{{ $viewPerm->id }}"
                                           id="perm_{{ $viewPerm->id }}"
                                           {{ in_array($viewPerm->id, $assignedPermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $viewPerm->id }}">
                                        <i class="ki-duotone ki-eye text-primary me-2"></i> Xem {{ $module }}
                                        <small class="d-block text-muted">{{ $viewPerm->description }}</small>
                                    </label>
                                </div>
                                @endif

                                @if($createPerm = $modulePermissions->firstWhere('slug', 'like', 'create-' . strtolower($module)))
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input module-{{ $module }}" type="checkbox" 
                                           name="permissions[]" value="{{ $createPerm->id }}"
                                           id="perm_{{ $createPerm->id }}"
                                           data-dependency="{{ $viewPerm->id ?? '' }}"
                                           {{ in_array($createPerm->id, $assignedPermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $createPerm->id }}">
                                        <i class="ki-duotone ki-plus-square text-success me-2"></i> Thêm {{ $module }}
                                        <small class="d-block text-muted">{{ $createPerm->description }}</small>
                                    </label>
                                </div>
                                @endif

                                @if($editPerm = $modulePermissions->firstWhere('slug', 'like', 'edit-' . strtolower($module)))
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input module-{{ $module }}" type="checkbox" 
                                           name="permissions[]" value="{{ $editPerm->id }}"
                                           id="perm_{{ $editPerm->id }}"
                                           data-dependency="{{ $viewPerm->id ?? '' }}"
                                           {{ in_array($editPerm->id, $assignedPermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $editPerm->id }}">
                                        <i class="ki-duotone ki-notepad-edit text-warning me-2"></i> Chỉnh sửa {{ $module }}
                                        <small class="d-block text-muted">{{ $editPerm->description }}</small>
                                    </label>
                                </div>
                                @endif

                                @if($deletePerm = $modulePermissions->firstWhere('slug', 'like', 'delete-' . strtolower($module)))
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input module-{{ $module }}" type="checkbox" 
                                           name="permissions[]" value="{{ $deletePerm->id }}"
                                           id="perm_{{ $deletePerm->id }}"
                                           data-dependency="{{ $viewPerm->id ?? '' }}"
                                           {{ in_array($deletePerm->id, $assignedPermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $deletePerm->id }}">
                                        <i class="ki-duotone ki-trash text-danger me-2"></i> Xoá {{ $module }}
                                        <small class="d-block text-muted">{{ $deletePerm->description }}</small>
                                    </label>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Special permissions -->
                        @if($modulePermissions->filter(function($item) use ($module) {
                            return !in_array(explode('-', $item->slug)[0], ['view', 'create', 'edit', 'delete']);
                        })->count() > 0)
                        <div class="col-md-6">
                            <h4 class="mb-3 fs-6 fw-semibold">Quyền nâng cao</h4>
                            <div class="d-flex flex-column gap-3">
                                @foreach($modulePermissions->filter(function($item) use ($module) {
                                    return !in_array(explode('-', $item->slug)[0], ['view', 'create', 'edit', 'delete']);
                                }) as $otherPerm)
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input module-{{ $module }}" type="checkbox" 
                                           name="permissions[]" value="{{ $otherPerm->id }}"
                                           id="perm_{{ $otherPerm->id }}"
                                           data-dependency="{{ $viewPerm->id ?? '' }}"
                                           {{ in_array($otherPerm->id, $assignedPermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $otherPerm->id }}">
                                        @php
                                            $action = explode('-', $otherPerm->slug)[0];
                                            $icon = match($action) {
                                                'approve' => '<i class="ki-duotone ki-check-circle text-success me-2"></i>',
                                                'assign' => '<i class="ki-duotone ki-send text-primary me-2"></i>',
                                                'configure' => '<i class="ki-duotone ki-setting-2 text-info me-2"></i>',
                                                'support' => '<i class="ki-duotone ki-message-text-2 text-warning me-2"></i>',
                                                default => '<i class="ki-duotone ki-shield-tick text-primary me-2"></i>'
                                            };
                                        @endphp
                                        {!! $icon !!} {{ ucfirst($action) }} {{ $module }}
                                        <small class="d-block text-muted">{{ $otherPerm->description }}</small>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach

            <div class="card-footer justify-end py-7.5 gap-2.5">
                <button type="button" class="btn btn-light btn-outline" id="reset-permissions">
                    Mặc định
                </button>
                <button type="button" class="btn btn-primary" id="save-permissions">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push("scripts")
<script>
    $(function() {
        // Calculate total permissions
        const totalPermissions = $('input[name="permissions[]"]').length;
        $('#total-count').text(totalPermissions);
        
        // Update permissions count
        function updatePermissionsCount() {
            const selectedPermissions = $('input[name="permissions[]"]:checked').length;
            $('#selected-count').text(selectedPermissions);
            
            const percentage = Math.round((selectedPermissions / totalPermissions) * 100);
            $('#selected-percentage').text(percentage + '%');
            $('#permission-progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage);
        }
        
        // Initial update
        updatePermissionsCount();
        
        // Handle dependencies
        $('input[name="permissions[]"]').on('change', function() {
            const isChecked = $(this).prop('checked');
            const permId = $(this).val();
            
            // If unchecked, uncheck all permissions that depend on this one
            if (!isChecked) {
                $(`input[data-dependency="${permId}"]`).prop('checked', false);
            }
            
            // If checked, make sure the dependency is also checked
            if (isChecked) {
                const dependencyId = $(this).data('dependency');
                if (dependencyId) {
                    $(`#perm_${dependencyId}`).prop('checked', true);
                }
            }
            
            updatePermissionsCount();
        });
        
        // Select all permissions
        $('#select-all-permissions').on('click', function(e) {
            e.preventDefault();
            $('input[name="permissions[]"]').prop('checked', true);
            updatePermissionsCount();
        });
        
        // Unselect all permissions
        $('#unselect-all-permissions').on('click', function(e) {
            e.preventDefault();
            $('input[name="permissions[]"]').prop('checked', false);
            updatePermissionsCount();
        });
        
        // Select module permissions
        $('.select-module').on('click', function(e) {
            e.preventDefault();
            const module = $(this).data('module');
            $(`.module-${module}`).prop('checked', true);
            updatePermissionsCount();
        });
        
        // Unselect module permissions
        $('.unselect-module').on('click', function(e) {
            e.preventDefault();
            const module = $(this).data('module');
            $(`.module-${module}`).prop('checked', false);
            updatePermissionsCount();
        });
        
        // Reset to default permissions
        $('#reset-permissions').on('click', function(e) {
            e.preventDefault();
            // Uncheck all
            $('input[name="permissions[]"]').prop('checked', false);
            
            // Check basic default permissions
            // Basic viewing permissions for common modules
            const defaultPermissions = [
                'view-dashboard', 'view-member', 'view-team', 'view-task', 
                'view-customer', 'view-contract'
            ];
            
            defaultPermissions.forEach(slug => {
                $(`input[name="permissions[]"]`).filter(function() {
                    return $(this).siblings('label').text().toLowerCase().includes(slug);
                }).prop('checked', true);
            });
            
            updatePermissionsCount();
        });
        
        // Save permissions
        $('#save-permissions').on('click', async function(e) {
            e.preventDefault();
            
            Notiflix.Block.dots('#permissions-form', 'Đang lưu quyền...');
            
            let permissions = [];
            $('input[name="permissions[]"]:checked').each(function() {
                permissions.push($(this).val());
            });
            
            try {
                let res = await axiosTemplate(
                    "post",
                    "/role/{{ $details['level']->id }}/{{ $details['department']->id }}/permissions",
                    null,
                    { permissions: permissions }
                );
                
                Notiflix.Block.remove('#permissions-form');
                
                if (res.data.status === 200) {
                    Notiflix.Notify.success(res.data.message);
                } else {
                    Notiflix.Notify.warning(res.data.message || "Đã có lỗi xảy ra khi lưu quyền!");
                }
            } catch (error) {
                Notiflix.Block.remove('#permissions-form');
                Notiflix.Notify.failure("Đã có lỗi xảy ra khi lưu quyền!");
                console.error(error);
            }
        });
    });
</script>
@endpush