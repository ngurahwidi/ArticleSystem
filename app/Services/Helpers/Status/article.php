<?php

if (!function_exists("errArticleGet")) {
    function errArticleGet($internalMsg = "", $status = null)
    {
        error(404, "Article not found!", $internalMsg, $status);
    }
}

if (!function_exists("errStatusId")) {
    function errStatusId($internalMsg = "", $status = null)
    {
        error(404, "StatusId not found!", $internalMsg, $status);
    }
}