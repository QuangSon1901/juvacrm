<?php

if (!function_exists('isActiveRoute')) {
    function isActiveRoute($route, $output = 'active') {
        return request()->routeIs($route) ? $output : '';
    }
}