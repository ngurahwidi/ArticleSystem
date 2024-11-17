<?php

if (!function_exists("errArticleGet")) {
    function errArticleGet($internalMsg = "", $status = null)
    {
        error(404, "Article not found!", $internalMsg, $status);
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
        error(400, "Article Already Favorited", $internalMsg, $status);
    }
}

if (!function_exists("errArticleUnFavorite")) {
    function errArticleUnFavorite($internalMsg = "", $status = null){
        error(400, "Article not in Favorites", $internalMsg, $status);
    }
}

if (!function_exists("errArticleSave")) {
    function errArticleSave($internalMsg = "", $status = null){
        error(400, "Cant save article", $internalMsg, $status);
    }

}

if (!function_exists("errArticleUpdate")) {
    function errArticleUpdate($internalMsg = "", $status = null){
        error(400, "Cant update article", $internalMsg, $status);
    }
}
