<?php

if (!function_exists("errDefault")) {
    function errDefault($internalMsg = "")
    {
        error(500, "An error occurred!", $internalMsg);
    }
}

if (!function_exists("errAuthentication")) {
    function errAuthentication($internalMsg = "", $status = null)
    {
        error(401, "Unauthenticated!", $internalMsg, $status);
    }
}

if (!function_exists("errForbidden")) {
    function errForbidden($internalMsg = "", $status = null)
    {
        error(403, "Forbidden!", $internalMsg, $status);
    }
}
