<?php

if (!function_exists("errParentNotFound")) {
    function errParentNotFound($internalMsg = "", $status =  null)
    {
        error(404, "Parent comment not found!", $internalMsg, $status);
    }
}

if (!function_exists("errCommentGet")) {
    function errCommentGet($internalMsg = "", $status = null)
    {
        error(404, "Comment not found!", $internalMsg, $status);
    }
}

if (!function_exists("errCommentSave")) {
    function errCommentSave($internalMsg = "", $status = null)
    {
        error(400, "Cant save comment", $internalMsg, $status);
    }
}

if (!function_exists("errCommentUpdate")) {
    function errCommentUpdate($internalMsg = "", $status = null)
    {
        error(400, "Cant update comment", $internalMsg, $status);
    }
}