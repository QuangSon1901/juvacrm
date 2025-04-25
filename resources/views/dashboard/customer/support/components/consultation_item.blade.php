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
        
        <!-- Phần đính kèm tệp -->
        @if (count($log['attachments']) > 0)
        <div class="card shadow-none mt-2">
            <div class="card-body p-3">
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