<div class="flex flex-col">
    <!-- Hiển thị các ngày trong tuần -->
    <div class="flex">
        <div class="w-[100px] border-r"></div>
        <div class="flex-1 grid grid-cols-7 gap-4">
            @foreach ($week_days as $day)
            <div class="text-center text-sm text-gray-900 py-4 week-day-cell" data-date="{{$day['full_date'] ?? ''}}">
                <p class="font-semibold">{{ $day['date'] }}</p>
                <p>{{ $day['day'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex">
        <!-- Hiển thị khung thời gian -->
        <div class="w-[100px] border-r">
            <ul>
                @foreach (range(8, 17) as $hour)
                <li class="relative h-[100px] text-sm text-gray-600 px-4 flex items-center justify-between">
                    <span>{{ $hour < 10 ? '0' . $hour . ':00' : $hour . ':00' }}</span>
                    <span class="absolute right-0 top-1/2 -translate-y-1/2 w-4 h-[2px] bg-neutral-500"></span>
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Hiển thị lịch hẹn trong tuần -->
        <div class="flex-1 grid grid-cols-7">
            @foreach (range(0, 6) as $dayIndex)
            <div class="relative w-full h-full {{$dayIndex == 6 ? '' : 'border-r'}} px-2">
                <div class="relative">
                    @foreach ($appointments as $appointment)
                    @php
                    // Chuyển đổi thành số nguyên rõ ràng
                    $startDayOfWeek = (int)date('w', strtotime($appointment['start_time']));
                    
                    // 0 = Sunday, 1 = Monday, ..., 6 = Saturday (theo định dạng PHP)
                    // Chuyển đổi để 0 = Monday, 1 = Tuesday, ..., 6 = Sunday (theo hiển thị của chúng ta)
                    $startDay = $startDayOfWeek == 0 ? 6 : $startDayOfWeek - 1;
                        
                    // Phân tích thời gian
                    $startHour = (int)date('H', strtotime($appointment['start_time']));
                    $startMinute = (int)date('i', strtotime($appointment['start_time']));
                    $startInMinutes = ($startHour - 8) * 60 + $startMinute;

                    $endHour = (int)date('H', strtotime($appointment['end_time']));
                    $endMinute = (int)date('i', strtotime($appointment['end_time']));
                    $endInMinutes = ($endHour - 8) * 60 + $endMinute;

                    // Tính toán vị trí và kích thước
                    $top = ($startInMinutes / 60) * 100 + 50;
                    $height = max(25, (($endInMinutes - $startInMinutes) / 60) * 100);
                    @endphp

                    @if ($dayIndex == $startDay)
                    <div class="absolute left-0 badge badge-outline badge-{{$appointment['color']}} items-start justify-start w-full cursor-pointer p-0" style="top: {{$top}}px; height: {{$height}}px;">
                        <div data-toggle="#reminder-week-{{$appointment['id']}}" data-toggle-class="hidden" class="toggle-badge flex flex-col gap-1 items-start justify-start overflow-hidden w-full h-full p-2">
                            <p class="text-xs font-semibold">{{$appointment['name']}}</p>
                            <span class="text-xs"><i class="ki-filled ki-time"></i> {{date('H:i', strtotime($appointment['start_time']))}} - {{date('H:i', strtotime($appointment['end_time']))}}</span>
                        </div>

                        <div class="hidden absolute top-[20px] left-0 z-10 lg:w-max toggle-modal" id="reminder-week-{{$appointment['id']}}">
                            <div class="card p-4">
                                <div class="absolute top-2 right-2" onclick="$(this).closest('.toggle-modal').addClass('hidden')">
                                    <button class="btn btn-xs btn-icon btn-light">
                                        <i class="ki-outline ki-cross"></i>
                                    </button>
                                </div>
                                <p class="text-sm font-semibold mb-2">{{$appointment['name']}}</p>
                                <span class="text-xs mb-1 text-gray-900"><i class="ki-filled ki-time"></i> {{date('H:i', strtotime($appointment['start_time']))}} - {{date('H:i', strtotime($appointment['end_time']))}}</span>
                                <p class="text-xs text-gray-900">{{$appointment['note']}}</p>
                                <div class="flex flex-wrap gap-1 mt-2">
                                    <button class="btn btn-sm btn-primary edit-appointment-btn"
                                           data-id="{{$appointment['id']}}"
                                           data-name="{{$appointment['name']}}"
                                           data-note="{{$appointment['note']}}"
                                           data-start="{{date('Y-m-d H:i:s', strtotime($appointment['start_time']))}}"
                                           data-end="{{date('Y-m-d H:i:s', strtotime($appointment['end_time']))}}"
                                           data-color="{{$appointment['color']}}"
                                           data-modal-toggle="#edit-appointment-modal">
                                        Chỉnh sửa
                                    </button>
                                    <button class="btn btn-sm btn-danger">
                                        Huỷ lịch
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>