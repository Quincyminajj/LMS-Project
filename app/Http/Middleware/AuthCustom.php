<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthCustom
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    // Cek apakah user sudah login (ada session identifier)
    if (!session('identifier')) {
      return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
    }

    // Cek apakah role ada
    if (!session('role')) {
      session()->flush();
      return redirect()->route('login')->with('error', 'Session tidak valid. Silakan login kembali.');
    }

    return $next($request);
  }
}
