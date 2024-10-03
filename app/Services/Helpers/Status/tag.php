<?php

if (!function_exists("errTagGet")) {
    function errTagGet($internalMsg = "", $status = null)
    {
        error(404, "Tag not found!", $internalMsg, $status);
    }
}