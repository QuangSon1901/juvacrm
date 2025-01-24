<div class="flex flex-col">
    <!-- Hiển thị các ngày trong tháng -->
    <div class="grid grid-cols-7 gap-2 p-4">
        @foreach (['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'] as $day)
        <div class="text-center text-sm font-semibold text-gray-900">{{ $day }}</div>
        @endforeach
    </div>

    <div class="grid grid-cols-7 gap-2 p-4">
        @php
        $date = \Carbon\Carbon::parse($currentDate);

        $currentMonth = $date->format('m');
        $currentYear = $date->format('Y');
        $daysInMonth = $date->daysInMonth;
        $firstDayOfMonth = $date->startOfMonth()->dayOfWeekIso;
        @endphp

        <!-- Thêm các ô trống cho ngày đầu tháng -->
        @foreach (range(1, $firstDayOfMonth - 1) as $emptyDay)
        <div class="border bg-gray-100"></div>
        @endforeach

        <!-- Hiển thị các ngày trong tháng -->
        @foreach (range(1, $daysInMonth) as $day)
        @php
        $currentDate = "$currentYear-$currentMonth-" . str_pad($day, 2, '0', STR_PAD_LEFT);
        @endphp
        <div class="border relative bg-white">
            <div class="absolute top-1 right-1 text-xs text-gray-400 font-bold">{{ $day }}</div>
            <div class="mt-6 space-y-1 px-2 pb-2">
                @foreach ($appointments as $appointment)
                @if (formatDateTime($appointment['start_time'], 'Y-m-d') === $currentDate)
                <div data-toggle="#reminder-month-{{$appointment['id']}}" data-toggle-class="hidden" class="toggle-badge badge badge-outline badge-{{ $appointment['color'] }} w-full cursor-pointer flex-col items-start justify-start">
                    <p class="text-xs font-semibold">{{ $appointment['name'] }}</p>
                    <span class="text-xs"><i class="ki-filled ki-time"></i> {{formatDateTime($appointment['start_time'], 'H:i')}} - {{formatDateTime($appointment['end_time'], 'H:i')}}</span>
                </div>
                <div class="hidden absolute top-[40px] left-0 z-10 lg:w-max toggle-modal" id="reminder-month-{{$appointment['id']}}">
                    <div class="card p-4">
                        <div class="absolute top-2 right-2" onclick="$(this).closest('.toggle-modal').addClass('hidden')">
                            <button class="btn btn-xs btn-icon btn-light">
                                <i class="ki-outline ki-cross"></i>
                            </button>
                        </div>
                        <p class="text-sm font-semibold mb-2">{{$appointment['name']}}</p>
                        <span class="text-xs mb-1 text-gray-900"><i class="ki-filled ki-time"></i> {{formatDateTime($appointment['start_time'], 'H:i')}} - {{formatDateTime($appointment['end_time'], 'H:i')}}</span>
                        <p class="text-xs text-gray-900">{{$appointment['note']}}</p>
                        <div class="flex flex-wrap gap-1 mt-2">
                            <button class="btn btn-sm btn-primary">
                                Chỉnh sửa
                            </button>
                            <button class="btn btn-sm btn-danger">
                                Huỷ lịch
                            </button>
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