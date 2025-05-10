@extends('dashboard.layouts.layout')
@section('dashboard_content')
@php
use Carbon\Carbon;
$now = Carbon::now();

@endphp
<div class="pb-5">
    <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center flex-wrap gap-1 lg:gap-5">
            <h1 class="font-medium text-base text-gray-900">
                Lịch làm việc của tôi
            </h1>
        </div>
        <div class="flex items-center flex-wrap gap-1.5 lg:gap-2.5">
            <div class="relative">
                <select class="select select-sm" id="month-filter">
                    @php
                        $currentYear = date('Y');
                        $selectedYear = substr($month, 0, 4);
                        $selectedMonth = substr($month, 5, 2);
                        // Năm trước, năm hiện tại và năm sau
                        $years = [$currentYear - 1, $currentYear, $currentYear + 1];
                    @endphp
                    
                    @foreach($years as $year)
                        @for($m = 1; $m <= 12; $m++)
                            @php
                                $monthPadded = str_pad($m, 2, '0', STR_PAD_LEFT);
                                $value = $year . '-' . $monthPadded;
                                $selected = ($year == $selectedYear && $monthPadded == $selectedMonth) ? 'selected' : '';
                                
                                // Tên tháng tiếng Việt
                                $monthNames = [
                                    1 => 'Tháng 1', 2 => 'Tháng 2', 3 => 'Tháng 3', 
                                    4 => 'Tháng 4', 5 => 'Tháng 5', 6 => 'Tháng 6',
                                    7 => 'Tháng 7', 8 => 'Tháng 8', 9 => 'Tháng 9', 
                                    10 => 'Tháng 10', 11 => 'Tháng 11', 12 => 'Tháng 12'
                                ];
                            @endphp
                            <option value="{{ $value }}" {{ $selected }}>{{ $monthNames[$m] }}, {{ $year }}</option>
                        @endfor
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary btn-sm" data-modal-toggle="#create-schedule-modal">
                <i class="ki-filled ki-plus me-1"></i>
                Đăng ký lịch làm việc
            </button>
        </div>
    </div>
</div>

<div class="container-fixed">
    <div class="grid gap-5 lg:gap-7.5">
        <!-- Thống kê -->
        <div class="grid !grid-cols-1 lg:!grid-cols-12 gap-5 mb-5">
            <div class="lg:col-span-12">
                <div class="grid !grid-cols-1 md:!grid-cols-4 gap-4">
                    <div class="card p-4 flex flex-row items-center">
                        <div class="bg-light-primary rounded-full p-2 mr-3">
                            <i class="ki-outline ki-calendar text-primary text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Tổng lịch</div>
                            <div class="text-xl font-bold">{{ $partTimeStats['total'] }}</div>
                        </div>
                    </div>
                    
                    <div class="card p-4 flex flex-row items-center">
                        <div class="bg-light-success rounded-full p-2 mr-3">
                            <i class="ki-outline ki-check text-success text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Đã duyệt</div>
                            <div class="text-xl font-bold">{{ $partTimeStats['approved'] }}</div>
                        </div>
                    </div>
                    
                    <div class="card p-4 flex flex-row items-center">
                        <div class="bg-light-warning rounded-full p-2 mr-3">
                            <i class="ki-outline ki-timer text-warning text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Chờ duyệt</div>
                            <div class="text-xl font-bold">{{ $partTimeStats['pending'] }}</div>
                        </div>
                    </div>
                    
                    <div class="card p-4 flex flex-row items-center">
                        <div class="bg-light-info rounded-full p-2 mr-3">
                            <i class="ki-outline ki-time text-info text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Tổng giờ làm</div>
                            <div class="text-xl font-bold">{{ number_format($partTimeStats['totalHours'], 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Danh sách lịch làm việc -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Danh sách lịch làm việc</h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Thời gian</th>
                                <th>Tổng giờ</th>
                                <th>Ghi chú</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $schedule)
                            <tr>
                                <td>{{ formatDateTime($schedule->schedule_date, 'd/m/Y') }}</td>
                                <td>{{ formatDateTime($schedule->start_time, 'H:i') }} - {{ formatDateTime($schedule->end_time, 'H:i') }}</td>
                                <td>{{ number_format($schedule->total_hours, 2) }}</td>
                                <td>
                                    <span class="badge badge-sm badge-{{ $schedule->getStatusClass() }}">
                                        {{ $schedule->getStatusText() }}
                                    </span>
                                </td>
                                <td>{{ $schedule->note }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex">
                                        <button class="btn btn-xs btn-icon btn-light me-1" onclick="showScheduleDetails({{ $schedule->id }})" title="Xem chi tiết">
                                            <i class="ki-outline ki-eye"></i>
                                        </button>
                                        
                                        @if($schedule->canCancel())
                                        <button class="btn btn-xs btn-icon btn-danger" onclick="cancelSchedule({{ $schedule->id }})" title="Hủy lịch">
                                            <i class="ki-outline ki-cross"></i>
                                        </button>
                                        @elseif($schedule->canRequestCancel())
                                        <button class="btn btn-xs btn-icon btn-warning" onclick="requestCancelSchedule({{ $schedule->id }})" title="Yêu cầu hủy">
                                            <i class="ki-outline ki-arrows-circle"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-6">
                                    <p class="text-gray-500">Chưa có lịch làm việc nào trong tháng này</p>
                                    <button class="btn btn-sm btn-primary mt-3" data-modal-toggle="#create-schedule-modal">
                                        <i class="ki-outline ki-plus me-1"></i> Đăng ký lịch làm việc
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($schedules->total() > 0)
                <div class="card-footer d-flex justify-content-end py-4">
                    {{ $schedules->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal đăng ký lịch làm việc -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="create-schedule-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Đăng ký lịch làm việc
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="create-schedule-form" class="grid gap-4 py-4">
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Ngày làm việc <span class="text-red-500">*</span>
                    </label>
                    <input class="input" type="text" name="schedule_date" data-flatpickr="true" placeholder="Chọn ngày" required>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm mb-1">
                            Giờ bắt đầu <span class="text-red-500">*</span>
                        </label>
                        <input class="input" type="text" name="start_time" data-flatpickr="true" data-flatpickr-type="time" placeholder="Giờ bắt đầu" required>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm mb-1">
                            Giờ kết thúc <span class="text-red-500">*</span>
                        </label>
                        <input class="input" type="text" name="end_time" data-flatpickr="true" data-flatpickr-type="time" placeholder="Giờ kết thúc" required>
                    </div>
                </div>
                
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Ghi chú
                    </label>
                    <textarea class="textarea" name="note" rows="2" placeholder="Nhập ghi chú (nếu có)"></textarea>
                </div>
                
                <div class="flex flex-col pt-2">
                    <button type="submit" class="btn btn-primary justify-center">
                        Đăng ký
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal chi tiết lịch làm việc -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="schedule-detail-modal" style="z-index: 90;">
    <div class="modal-content max-w-[500px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Chi tiết lịch làm việc
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="schedule-detail-content" class="grid gap-5 px-0 py-5">
                <!-- Nội dung chi tiết sẽ được load bằng AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Modal yêu cầu hủy lịch -->
<div class="modal hidden" data-modal="true" data-modal-disable-scroll="false" id="request-cancel-modal" style="z-index: 90;">
    <div class="modal-content max-w-[400px] top-5 lg:top-[15%]">
        <div class="modal-header pr-2.5">
            <h3 class="modal-title">
                Yêu cầu hủy lịch làm việc
            </h3>
            <button class="btn btn-sm btn-icon btn-light btn-clear btn-close shrink-0" data-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="request-cancel-form" class="grid gap-4 py-4">
                <input type="hidden" name="id" id="cancel-schedule-id">
                
                <div class="flex flex-col gap-2">
                    <label class="font-medium text-sm mb-1">
                        Lý do hủy <span class="text-red-500">*</span>
                    </label>
                    <textarea class="textarea" name="reason" rows="3" placeholder="Nhập lý do hủy lịch" required></textarea>
                </div>
                
                <div class="flex flex-col pt-2">
                    <button type="submit" class="btn btn-danger justify-center">
                        Yêu cầu hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Khởi tạo flatpickr cho các trường ngày và giờ
        flatpickrMake($("input[name='schedule_date']"), 'date');
        flatpickrMake($("input[name='start_time']"), 'time', {enableSeconds: false, noCalendar: true, enableTime: true, dateFormat: 'H:i'});
        flatpickrMake($("input[name='end_time']"), 'time', {enableSeconds: false, noCalendar: true, enableTime: true, dateFormat: 'H:i'});
        
        $('#month-filter').on('change', function() {
            window.location.href = "{{ route('dashboard.profile.my-schedule') }}?month=" + $(this).val();
        });
        
        // Xử lý form đăng ký lịch làm việc
        $('#create-schedule-form').on('submit', async function(e) {
            e.preventDefault();
            
            const scheduleDate = $(this).find('input[name="schedule_date"]').val();
            const startTime = $(this).find('input[name="start_time"]').val();
            const endTime = $(this).find('input[name="end_time"]').val();
            const note = $(this).find('textarea[name="note"]').val();
            
            // Kiểm tra form
            if (!scheduleDate || !startTime || !endTime) {
                showAlert('warning', 'Vui lòng điền đầy đủ thông tin bắt buộc');
                return;
            }
            
            try {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                
                const res = await axiosTemplate('post', "{{ route('dashboard.profile.my-schedule.create') }}", null, {
                    schedule_date: scheduleDate,
                    start_time: startTime,
                    end_time: endTime,
                    note: note
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#create-schedule-modal')).hide();
                    
                    // Làm mới trang sau khi đăng ký thành công
                    window.location.reload();
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi đăng ký lịch làm việc');
                console.error(error);
            } finally {
                $(this).find('button[type="submit"]').prop('disabled', false).html('Đăng ký');
            }
        });
        
        // Xử lý form yêu cầu hủy lịch
        $('#request-cancel-form').on('submit', async function(e) {
            e.preventDefault();
            
            const id = $('#cancel-schedule-id').val();
            const reason = $(this).find('textarea[name="reason"]').val();
            
            try {
                $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="ki-duotone ki-spinner-dot fs-2 animate-spin me-1"></i> Đang xử lý...');
                
                const res = await axiosTemplate('post', "{{ route('dashboard.profile.my-schedule.request-cancel') }}", null, {
                    id: id,
                    reason: reason
                });
                
                if (res.data.status === 200) {
                    showAlert('success', res.data.message);
                    KTModal.getInstance(document.querySelector('#request-cancel-modal')).hide();
                    
                    // Làm mới trang sau khi yêu cầu hủy thành công
                    window.location.reload();
                } else {
                    showAlert('warning', res.data.message);
                }
            } catch (error) {
                showAlert('error', 'Đã xảy ra lỗi khi yêu cầu hủy lịch làm việc');
                console.error(error);
            } finally {
                $(this).find('button[type="submit"]').prop('disabled', false).html('Yêu cầu hủy');
            }
        });
    });
    
    // Hàm hủy lịch chưa được duyệt
    function cancelSchedule(id) {
        Notiflix.Confirm.show(
            'Hủy lịch làm việc',
            'Bạn có chắc chắn muốn hủy lịch làm việc này?',
            'Đồng ý',
            'Hủy bỏ',
            async function() {
                try {
                    const res = await axiosTemplate('post', "{{ route('dashboard.profile.my-schedule.cancel') }}", null, {
                        id: id
                    });
                    
                    if (res.data.status === 200) {
                        showAlert('success', res.data.message);
                        window.location.reload();
                    } else {
                        showAlert('warning', res.data.message);
                    }
                } catch (error) {
                    showAlert('error', 'Đã xảy ra lỗi khi hủy lịch làm việc');
                    console.error(error);
                }
            }
        );
    }
    
    // Hàm yêu cầu hủy lịch đã được duyệt
    function requestCancelSchedule(id) {
        $('#cancel-schedule-id').val(id);
        KTModal.getInstance(document.querySelector('#request-cancel-modal')).show();
    }
    
    // Hàm xem chi tiết lịch
    function showScheduleDetails(id) {
        // Hiển thị modal
        KTModal.getInstance(document.querySelector('#schedule-detail-modal')).show();
        
        // Hiển thị loading
        $('#schedule-detail-content').html(`
            <div class="flex justify-center items-center p-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);
        
        // Gọi API lấy chi tiết
        axiosTemplate('get', "/profile/my-schedule/" + id + "/detail", null, null)
            .then(res => {
                if (res.data.status === 200) {
                    $('#schedule-detail-content').html(res.data.content);
                } else {
                    $('#schedule-detail-content').html(`
                        <div class="text-center text-danger">
                            ${res.data.message || 'Không thể tải thông tin chi tiết'}
                        </div>
                    `);
                }
            })
            .catch(error => {
                $('#schedule-detail-content').html(`
                    <div class="text-center text-danger">
                        Đã xảy ra lỗi khi tải thông tin
                    </div>
                `);
                console.error(error);
            });
    }
</script>
@endpush