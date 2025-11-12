<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $nip = $this->nip;
        $password = $this->password;

        // Cek guru
        $guru = DB::table('rb_guru')->where('nip', $nip)->first();
        if ($guru && $guru->password === $password) {
            session([
                'identifier' => $guru->nip,
                'user_name'  => $guru->nama_guru,
                'role'       => 'guru',
            ]);
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // Cek siswa
        $siswa = DB::table('rb_siswa')->where('nisn', $nip)->orWhere('nipd', $nip)->first();
        if ($siswa && $siswa->password === $password) {
            session([
                'identifier' => $siswa->nisn,
                'user_name'  => $siswa->nama,
                'role'       => 'siswa',
            ]);
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // Jika gagal
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'nip' => trans('auth.failed'),
        ]);
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nip' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::lower($this->nip).'|'.$this->ip();
    }
}
