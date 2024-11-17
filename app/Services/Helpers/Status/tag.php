<?php

if (!function_exists("errTagGet")) {
    function errTagGet($internalMsg = "", $status = null)
    {
        error(404, "Tag not found!", $internalMsg, $status);
    }
}

if (!function_exists("errTagSave")) {
    function errTagSave($internalMsg = "", $status = null)
    {
        error(400, "Cant save tag", $internalMsg, $status);
    }
}

if (!function_exists("errTagUpdate")) {
    function errTagUpdate($internalMsg = "", $status = null)
    {
        error(400, "Cant update tag", $internalMsg, $status);
    }
}