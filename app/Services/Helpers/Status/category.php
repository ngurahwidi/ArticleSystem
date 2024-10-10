<?php

if (!function_exists("errCategoryGet")) {
    function errCategoryGet($internalMsg = "", $status = null)
    {
        error(404, "Category not found!", $internalMsg, $status);
    }
}

if (!function_exists("errCategorySave")) {
    function errCategorySave($internalMsg = "", $status = null)
    {
        error(400, "Cant save category", $internalMsg, $status);
    }
}

if (!function_exists("errCategoryUpdate")) {
    function errCategoryUpdate($internalMsg = "", $status = null)
    {
        error(400, "Cant update category", $internalMsg, $status);
    }
}