@if(count($notifications) > 0)
    @foreach($notifications as $notification)
        <div class="notification-item flex items-start p-5 hover:bg-light group/item {{ $notification->is_read ? 'bg-gray-50 is-read' : '' }}" 
             data-id="{{ $notification->id }}" 
             data-url="{{ $notification->action_url }}">
            <div class="relative shrink-0 mt-1 me-4">
                <div class="notification-icon rounded-full size-10 flex items-center justify-center bg-{{ $notification->icon_color }}-100">
                    <i class="notification-icon-class {{ $notification->icon }} text-{{ $notification->icon_color }}"></i>
                </div>
                <span class="size-2 badge badge-circle absolute top-0 right-0 ring-1 ring-light transform translate-x-1/2 -translate-y-1/2 {{ $notification->is_read ? 'bg-gray-300' : 'bg-primary' }}"></span>
            </div>
            
            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start mb-1">
                    <h4 class="notification-title text-base font-medium text-gray-900 mb-1 group-hover/item:text-primary">{{ $notification->title }}</h4>
                    <span class="notification-time text-xs text-gray-500">{{ $notification->getTimeAgo() }}</span>
                </div>
                <p class="notification-content text-sm text-gray-700 mb-2">{{ $notification->content }}</p>
                <div class="flex items-center gap-2">
                    <div class="notification-actions flex items-center gap-2">
                        @if(!$notification->is_read)
                        <button class="mark-read-btn btn btn-icon btn-xs btn-light-primary" title="Đánh dấu đã đọc">
                            <i class="ki-outline ki-check text-xs"></i>
                        </button>
                        @endif
                        <button class="delete-btn btn btn-icon btn-xs btn-light-danger" title="Xóa thông báo">
                            <i class="ki-outline ki-trash text-xs"></i>
                        </button>
                    </div>
                    @if($notification->action_url)
                    <a href="{{ $notification->action_url }}" class="ms-auto notification-link text-xs text-primary hover:text-primary-active flex items-center">
                        <span>Xem chi tiết</span>
                        <i class="ki-filled ki-arrow-right ms-1"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="text-center py-10">
        <div class="mb-4">
            <i class="ki-filled ki-notification text-gray-400 text-5xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-700 mb-1">Không có thông báo</h3>
        <p class="text-gray-500">{{ isset($is_read) ? ($is_read ? 'Bạn chưa có thông báo đã đọc' : 'Bạn chưa có thông báo chưa đọc') : 'Bạn chưa có thông báo nào' }}</p>
    </div>
@endif