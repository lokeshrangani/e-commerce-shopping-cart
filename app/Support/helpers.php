<?php

use App\Models\User;
use App\Models\UserActivity;

if (! function_exists('activity_log')) {
    function activity_log(User $user, string $action, array $meta = []): void
    {
        UserActivity::create([
            'user_id' => $user->id,
            'action' => $action,
            'meta' => $meta,
        ]);
    }
}
