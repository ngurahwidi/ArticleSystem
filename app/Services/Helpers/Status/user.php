<?php

if (!function_exists("errUserCreate")) {
    function errUserCreate($internalMsg = "", $status = null)
    {
        error(400, "Cant create user", $internalMsg, $status);
    }
}

if (!function_exists("errUserRole")) {
    function errUserRole($internalMsg = "", $status = null)
    {
        error(400, "Cant create user role", $internalMsg, $status);
    }
}
