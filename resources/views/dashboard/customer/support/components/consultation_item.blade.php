@foreach ($data as $log)
<div class="flex items-start relative">
    @if ($log['index'] != count($data))
    <div class="w-9 start-0 top-9 absolute bottom-0 rtl:-translate-x-1/2 translate-x-1/2 border-s border-s-gray-300"></div>
    @endif
    <div class="flex items-center justify-center shrink-0 rounded-full bg-gray-100 border border-gray-300 size-9 text-gray-600">
        <i class="ki-filled ki-user text-base">
        </i>
    </div>
    <div class="ps-2.5 mb-5 text-md grow">
        <div class="flex flex-col gap-1">
            <div class="text-sm text-gray-800">
                <span class="badge badge-sm badge-outline">
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
                <span style="overflow-wrap: anywhere;">{{$log['message']}}</span>
            </div>
            <span class="text-xs text-gray-600">
                {{date('H:i:s d-m-Y', strtotime($log['created_at']))}}
            </span>
        </div>
        @if (count($log['attachments']) > 0)
        <div class="card shadow-none mt-2">
            <div class="card-body">
                <div class="grid gap-2">
                    @foreach ($log['attachments'] as $attachment)
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2.5">
                            @if (Str::startsWith($attachment['type'], 'image/') && $attachment['extension'] != 'svg')
                            <img class="w-[30px]" alt="{{$attachment['extension']}}.svg" src="https://drive.google.com/thumbnail?id={{ $attachment['driver_id'] }}&sz=w56">
                            @else
                            <img class="w-[30px]" alt="{{$attachment['extension']}}.svg" src="{{asset('assets/images/file-types/' . $attachment['extension'] . '.svg')}}">
                            @endif
                            <div class="flex flex-col">
                                <a href="https://drive.google.com/file/d/{{$attachment['driver_id']}}/view" target="_blank" style="overflow-wrap: anywhere;" class="text-sm font-medium text-gray-900 cursor-pointer hover:text-primary mb-px">
                                    {{$attachment['name']}}
                                </a>
                                <span class="text-xs text-gray-700">
                                    {{formatBytes($attachment['size'])}} - <a href="https://drive.google.com/uc?id={{$attachment['driver_id']}}&export=download" class="text-primary text-xs font-semibold">Tải xuống</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endforeach