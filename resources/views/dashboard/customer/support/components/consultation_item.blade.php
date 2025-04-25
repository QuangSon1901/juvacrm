@foreach ($data as $log)
<div class="flex items-start relative message-container">
    @if ($log['index'] != count($data))
    <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300"></div>
    @endif
    <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
        <i class="ki-filled ki-user text-base">
        </i>
    </div>
    <div class="ps-2.5 mb-5 text-md grow">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2">
                <div class="text-sm text-gray-800">
                    <span class="badge badge-sm 
                        @if ($log['status'] == 0) badge-primary
                        @elseif ($log['status'] == 1) badge-warning
                        @elseif ($log['status'] == 2) badge-success
                        @elseif ($log['status'] == 3) badge-info
                        @elseif ($log['status'] == 4) badge-danger
                        @elseif ($log['status'] == 5) badge-gray
                        @else badge-neutral @endif">
                        @if ($log['status'] == 0)
                        Hỏi nhu cầu khách hàng
                        @elseif ($log['status'] == 1)
                        Tư vấn gói
                        @elseif ($log['status'] == 2)
                        Lập hợp đồng
                        @elseif ($log['status'] == 3)
                        Gửi bảng giá
                        @elseif ($log['status'] == 4)
                        Khách từ chối
                        @elseif ($log['status'] == 5)
                        Đặt lịch tư vấn lại
                        @else
                        Hành động không xác định
                        @endif
                    </span>
                </div>
                <div class="text-xs text-gray-500">
                    Bởi: {{$log['user']['name'] ?? 'Nhân viên'}}
                </div>
            </div>
            <div class="mt-1">
                <span style="overflow-wrap: anywhere;" class="text-sm">{{$log['message']}}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <span class="text-xs text-gray-600">
                    {{date('H:i:s d-m-Y', strtotime($log['created_at']))}}
                </span>
                
                @if($log['has_follow_up'])
                <span class="badge badge-sm badge-light-warning">
                    <i class="ki-filled ki-calendar-tick text-xs mr-1"></i>
                    Hẹn: {{date('d/m/Y H:i', strtotime($log['follow_up_date']))}}
                </span>
                @endif
            </div>
        </div>
        
        <!-- Phần đính kèm tệp với hiển thị cải tiến -->
        @if (count($log['attachments']) > 0)
        <div class="mt-3 bg-gray-50 rounded-lg p-3 border border-gray-200">
            <h6 class="text-xs font-medium text-gray-700 mb-2">Tệp đính kèm</h6>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                @foreach ($log['attachments'] as $attachment)
                <div class="file-item-preview relative flex items-center p-2 rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="mr-3 w-10 h-10 flex items-center justify-center shrink-0">
                        @if (Str::startsWith($attachment['type'], 'image/') && $attachment['extension'] != 'svg')
                        <a href="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/v1/uploads/{{ $attachment['driver_id'] }}" 
                           target="_blank" class="cursor-zoom-in">
                            <img src="https://res.cloudinary.com/{{env('CLOUDINARY_CLOUD_NAME')}}/image/upload/w_80,h_80,c_fill,q_auto,f_auto/uploads/{{ $attachment['driver_id'] }}" 
                                 class="max-w-full max-h-full object-contain" 
                                 alt="Image preview">
                        </a>
                        @else
                        <img src="{{asset('assets/images/file-types/' . $attachment['extension'] . '.svg')}}" 
                             class="max-w-full max-h-full object-contain" 
                             alt="File preview">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ formatBytes($attachment['size']) }}</p>
                    </div>
                    <a href="https://drive.google.com/uc?id={{$attachment['driver_id']}}&export=download" 
                       class="ml-2 p-1 text-primary hover:text-primary-dark transition-colors duration-200"
                       title="Tải xuống">
                        <i class="ki-filled ki-download"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endforeach

<script>
// Script để hiển thị hình ảnh đính kèm phóng to khi nhấp vào
document.addEventListener('DOMContentLoaded', function() {
    // Tạo lightbox modal nếu chưa có
    if (!document.getElementById('image-lightbox')) {
        const lightbox = document.createElement('div');
        lightbox.id = 'image-lightbox';
        lightbox.className = 'fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 hidden';
        lightbox.innerHTML = `
            <div class="relative max-w-4xl max-h-[90vh] p-2">
                <button class="absolute top-4 right-4 text-white text-2xl">&times;</button>
                <img src="" alt="Lightbox image" class="max-w-full max-h-[85vh] object-contain">
            </div>
        `;
        document.body.appendChild(lightbox);
        
        // Xử lý đóng lightbox
        lightbox.addEventListener('click', function() {
            lightbox.classList.add('hidden');
        });
    }
    
    // Thêm sự kiện click cho các hình ảnh đính kèm
    document.querySelectorAll('.cursor-zoom-in').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const lightbox = document.getElementById('image-lightbox');
            const img = lightbox.querySelector('img');
            img.src = this.href;
            lightbox.classList.remove('hidden');
        });
    });
});
</script>