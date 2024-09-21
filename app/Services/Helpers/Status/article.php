<?php

if (!function_exists("errArticleGet")) {
    function errArticleGet($internalMsg = "", $status = null)
    {
        error(404, "Article not found!", $internalMsg, $status);
    }
}

if (!function_exists("errStatusNotFound")) {
    function errStatusNotFound($internalMsg = "", $status = null)
    {
        error(404, "StatusId not found!", $internalMsg, $status);
    }
}

if (!function_exists("errArticleCreate")) {
    function  errArticleValidStatus($internalMsg = "", $status = null)
    {
        error(403, "error", $internalMsg, $status);
    }
}