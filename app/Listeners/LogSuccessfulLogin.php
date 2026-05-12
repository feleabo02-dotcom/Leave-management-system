<?php

namespace App\Listeners;

use App\Models\LoginHistory;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        $request = Request::instance();

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        LoginHistory::create([
            'user_id'       => $user->id,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'device'        => $request->header('sec-ch-ua-platform'),
            'browser'       => $request->header('sec-ch-ua'),
            'platform'      => $request->header('sec-ch-ua-platform'),
            'success'       => true,
            'logged_in_at'  => now(),
        ]);
    }
}
