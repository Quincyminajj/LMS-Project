<?php

namespace App\Http\Middleware;

use Closure;

class Authenticate
{
    public function handle($request, Closure $next)
    {
        if (!session('identifier') || !session('role')) {
            return redirect()->route('login');
        }

        // Tambahkan data user ke request (optional)
        $request->merge([
            'sia_user' => [
                'id' => session('identifier'),
                'name' => session('user_name'),
                'role' => session('role'),
            ]
        ]);

        return $next($request);
    }
}
