<?php

if (!function_exists("has_role")) {

    /**
     * @param $roleIds
     * @param $user
     *
     * @return bool
     */
    function has_role($roleIds, $user = null): bool
    {
        if (!$user) {
            $user = auth()->guard('')->user();
            if (!$user) {
                return false;
            }
        }

        if (is_string($roleIds)) {
            $roleIds = [$roleIds];
        }

        return in_array($user->roleId, $roleIds);
    }

}
