@if(count($notifications) > 0)
    @foreach($notifications as $notification)
        <div class="flex grow gap-2.5 px-5 py-2 hover:bg-light notification-item" data-id="{{ $notification->id }}">
            <div class="relative shrink-0 mt-0.5">
                @if($notification->sender_id)
                    <img alt="{{ optional($notification->sender)->name }}" class="rounded-full size-8" src="{{ asset('assets/images/logo/favicon.png') }}">
                @else
                    <div class="rounded-full size-8 flex items-center justify-center bg-{{ $notification->icon_color }}-light">
                        <i class="{{ $notification->icon }} text-{{ $notification->icon_color }}"></i>
                    </div>
                @endif
                <span class="size-1.5 badge badge-circle badge-{{ $notification->icon_color }} absolute top-7 end-0.5 ring-1 ring-light transform -translate-y-1/2"></span>
            </div>
            <div class="flex flex-col gap-1 notification-content" data-url="{{ $notification->action_url }}">
                <div class="text-2sm font-medium mb-px">
                    <span class="text-gray-900 font-semibold">{{ $notification->title }}</span>
                </div>
                <div class="text-2xs text-gray-700">{{ $notification->content }}</div>
                <span class="flex items-center text-2xs font-medium text-gray-500">
                    {{ $notification->getTimeAgo() }}
                </span>
            </div>
            <div class="shrink-0 ml-auto">
                <button class="btn btn-icon btn-sm btn-light mark-read-btn" data-id="{{ $notification->id }}" title="Đánh dấu đã đọc">
                    <i class="ki-outline ki-check text-xs"></i>
                </button>
            </div>
        </div>
    @endforeach
@else
    <div class="px-5 py-10 text-center">
        <div class="text-gray-600">Không có thông báo mới</div>
    </div>
@endif