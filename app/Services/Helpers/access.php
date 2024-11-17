<?php

if (!function_exists('has_role')) {

    function has_role($roleIds, $user = null): bool
    {
        if (!$user) {
            $user = auth()->user();
            if (!$user) {
                return false;
            }
        }

        if (is_int($roleIds)) {
            $roleIds = [$roleIds];
        }

        return in_array($user->roleId, $roleIds);
    }
}
