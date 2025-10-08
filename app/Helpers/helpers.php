<?php

if (!function_exists('setActive')) {
    function setActive($routes)
    {
        if (is_array($routes)) {
            foreach ($routes as $route) {
                if (request()->routeIs($route)) {
                    return 'activePage';
                }
            }
        } else {
            if (request()->routeIs($routes)) {
                return 'activePage';
            }
        }
        return '';
    }
}
