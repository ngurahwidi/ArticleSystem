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

if (!function_exists("errArticleCategory")) {
    function errArticleCategory($internalMsg = "", $status = null)
    {
        error(400, "error article category must be publish", $internalMsg, $status);
    }


}
if (!function_exists("errArticleTag")) {
    function errArticleTag($internalMsg = "", $status = null)
    {
        error(400, "error article tag must be publish", $internalMsg, $status);
    }

}

if (!function_exists("errArticleFavorite")) {
    function errArticleFavorite($internalMsg = "", $status = null){
        error(400, "error article favorite", $internalMsg, $status);
    }
}