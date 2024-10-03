<?php

if (!function_exists("errCategoryGet")) {
    function errCategoryGet($internalMsg = "", $status = null)
    {
        error(404, "Category not found!", $internalMsg, $status);
    }
}