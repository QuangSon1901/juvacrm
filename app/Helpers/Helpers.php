<?php

use App\Services\LogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

if (!function_exists('isActiveRoute')) {
    function isActiveRoute($route, $output = 'active')
    {
        if (is_array($route)) {
            foreach ($route as $r) {
                if (request()->routeIs($r)) {
                    return $output;
                }
            }
            return '';
        }

        return request()->routeIs($route) ? $output : '';
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($timestamp)
    {
        // Lấy thời gian hiện tại
        $now = \Carbon\Carbon::now();
        // Tạo đối tượng Carbon từ timestamp
        $time = \Carbon\Carbon::createFromTimestamp($timestamp);

        // Tính khoảng cách thời gian
        $diffInSeconds = $now->diffInSeconds($time);
        $diffInMinutes = $now->diffInMinutes($time);
        $diffInHours = $now->diffInHours($time);
        $diffInDays = $now->diffInDays($time);
        $diffInMonths = $now->diffInMonths($time);
        $diffInYears = $now->diffInYears($time);

        if ($diffInYears > 0) {
            return $diffInYears . ' năm';
        } elseif ($diffInMonths > 0) {
            return $diffInMonths . ' tháng';
        } elseif ($diffInDays > 0) {
            return $diffInDays . ' ngày';
        } elseif ($diffInHours > 0) {
            $minutes = $diffInMinutes % 60;
            return $diffInHours . ' giờ' . ($minutes > 0 ? ' ' . $minutes . ' phút' : '') . '';
        } elseif ($diffInMinutes > 0) {
            return $diffInMinutes . ' phút';
        } else {
            return $diffInSeconds . ' giây';
        }
    }
}

/**
 * Format giá tiền.
 *
 * @param float|int $amount Giá trị cần format.
 * @param int $decimals Số thập phân tối đa (default = 0).
 * @param string $decimalSeparator Ký tự phân cách phần thập phân (default = '.').
 * @param string $thousandsSeparator Ký tự phân cách hàng ngàn (default = ',').
 * @return string
 */
if (!function_exists('formatCurrency')) {
    function formatCurrency($amount, $decimals = 0, $decimalSeparator = '.', $thousandsSeparator = ',')
    {
        if (!is_numeric($amount)) {
            return '0';
        }
        return number_format($amount, $decimals, $decimalSeparator, $thousandsSeparator);
    }
}

/**
 * Remove format giá tiền.
 *
 * @param string $formattedAmount Giá trị đã được format.
 * @param string $decimalSeparator Ký tự phân cách phần thập phân (default = '.').
 * @param string $thousandsSeparator Ký tự phân cách hàng ngàn (default = ',').
 * @return float
 */
if (!function_exists('removeFormatCurrency')) {
    function removeFormatCurrency($formattedAmount, $decimalSeparator = '.', $thousandsSeparator = ',')
    {
        $unformattedAmount = str_replace([$thousandsSeparator, $decimalSeparator], ['', '.'], $formattedAmount);
        return (float)$unformattedAmount;
    }
}

/**
 * Format ngày giờ từ định dạng đầu vào sang định dạng đầu ra.
 *
 * @param string $datetime Ngày giờ cần format.
 * @param string $inputFormat Định dạng của ngày giờ đầu vào (default = 'Y-m-d H:i:s').
 * @param string $outputFormat Định dạng của ngày giờ đầu ra (default = 'd/m/Y H:i:s').
 * @return string|null
 */
if (!function_exists('formatDateTime')) {
    function formatDateTime($datetime, $outputFormat = 'd-m-Y H:i:s', $inputFormat = 'Y-m-d H:i:s')
    {
        try {
            $date = Carbon::createFromFormat($inputFormat, $datetime);
            return $date->format($outputFormat);
        } catch (\Exception $e) {
            return '';
        }
    }
}

/**
 * Chuyển đổi byte thành định dạng dễ đọc (MB, GB, TB, ...)
 */
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}


/**
 * Helper để thực hiện Try-Catch và log.
 *
 * @param \Illuminate\Http\Request $request Request hiện tại.
 * @param callable $callback Hàm thực thi chính.
 * @param callable|null $logCallback (optional) Hàm log tùy chỉnh.
 * @return mixed
 */
if (!function_exists('tryCatchHelper')) {
    function tryCatchHelper($request, callable $callback, ?callable $logCallback = null)
    {
        try {
            $response = $callback();
            if ($request->isMethod('POST')) {
                if ($logCallback) $logCallback($request, $response->getData());
                else {
                    LogService::saveLog([
                        'action' => TASK_ENUM_LOG,
                        'ip' => $request->getClientIp(),
                        'details' => sprintf(
                            'URL: %s, Method: %s, Payload: %s',
                            $request->fullUrl(),
                            $request->method(),
                            json_encode($request->all())
                        ),
                        'fk_key' => null,
                        'fk_value' => null,
                    ]);
                }
            }

            return $response;
        } catch (\Exception $e) {
            LogService::saveLog([
                'action' => ERROR_ENUM_LOG,
                'ip' => $request->getClientIp(),
                'details' => $e->getMessage(),
                'fk_key' => null,
                'fk_value' => null,
            ]);

            return response()->json([
                'status' => 500,
                'message' => 'Đã xảy ra lỗi. Vui lòng thử lại.',
            ]);
        }
    }
}


if (!function_exists('hasPermission')) {
    /**
     * Kiểm tra người dùng hiện tại có quyền không
     *
     * @param string|array $permission
     * @return bool
     */
    function hasPermission($permission)
    {
        $user = Auth::user();
        return $user ? $user->hasPermission($permission) : false;
    }
}