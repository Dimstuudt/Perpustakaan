<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Handle email verification link.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Jika sudah diverifikasi sebelumnya
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/login')->with('status', 'Email kamu sudah diverifikasi, silakan login.');
        }

        // Tandai email sebagai terverifikasi
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Logout user supaya login ulang setelah verifikasi
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect ke halaman login dengan pesan sukses
        return redirect('/login')->with('status', 'Email berhasil diverifikasi, silakan login ulang.');
    }
}
