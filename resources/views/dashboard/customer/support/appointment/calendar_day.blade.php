<div class="flex">
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
    <div class="px-4" style="flex: 1;">
        <div class="relative w-full h-full">
            @foreach ($appointments as $appointment)
            @php
            $startHour = (int)date('H', strtotime($appointment['start_time']));
            $startMinute = (int)date('i', strtotime($appointment['start_time']));
            $startInMinutes = ($startHour - 8) * 60 + $startMinute;

            $endHour = (int)date('H', strtotime($appointment['end_time']));
            $endMinute = (int)date('i', strtotime($appointment['end_time']));
            $endInMinutes = ($endHour - 8) * 60 + $endMinute;

            $top = ($startInMinutes / 60) * 100 + 50;
            $height = max(25, (($endInMinutes - $startInMinutes) / 60) * 100);

            $lineTimeTop = $top;
            $lineTimeEnd = $top + $height;
            @endphp
            <div class="absolute p-0 left-0 badge badge-outline badge-{{$appointment['color']}} items-start justify-start w-full cursor-pointer" style="top: {{$top}}px; height: {{$height}}px;">
                <div data-toggle="#reminder-{{$appointment['id']}}" data-toggle-class="hidden" class="toggle-badge flex p-2 flex-col gap-1 items-start justify-start overflow-hidden w-full h-full">
                    <p class="text-xs font-semibold">{{$appointment['name']}}</p>
                    <span class="text-xs"><i class="ki-filled ki-time"></i> {{date('H:i', strtotime($appointment['start_time']))}} - {{date('H:i', strtotime($appointment['end_time']))}}</span>
                </div>

                <div class="hidden absolute top-[20px] left-0 z-10 toggle-modal" id="reminder-{{$appointment['id']}}">
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
            @if ($startMinute != 0)
            <div class="absolute w-[116px] text-sm text-{{$appointment['color']}} px-4 flex items-center justify-between" style="top: {{$lineTimeTop}}px; transform: translate(-116px, -.7rem);">
                <span>{{date('H:i', strtotime($appointment['start_time']))}}</span>
                <span class="absolute right-0 top-1/2 -translate-y-1/2 w-12 h-[1px] text-{{$appointment['color']}} bg-dashed"></span>
            </div>
            @endif
            @if ($endMinute != 0)
            <div class="absolute w-[116px] text-sm text-{{$appointment['color']}} px-4 flex items-center justify-between" style="top: {{$lineTimeEnd}}px; transform: translate(-116px, -.7rem);">
                <span>{{date('H:i', strtotime($appointment['end_time']))}}</span>
                <span class="absolute right-0 top-1/2 -translate-y-1/2 w-12 h-[1px] text-{{$appointment['color']}} bg-dashed"></span>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>