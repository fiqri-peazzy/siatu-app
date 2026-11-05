<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
#[Title('Login - SITU-SMA')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected array $rules = [
        'email' => ['required', 'email'],
        'password' => ['required'],
    ];

    public function login()
    {
        $this->validate();

        // Batasi tebakan password: 5 percobaan / menit per email + IP
        $kunci = 'login:'.strtolower($this->email).'|'.request()->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($kunci, 5)) {
            $detik = \Illuminate\Support\Facades\RateLimiter::availableIn($kunci);
            $this->addError('email', "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik.");

            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            \Illuminate\Support\Facades\RateLimiter::clear($kunci);
            session()->regenerate();
            
            if (Auth::user()->hasRole('guru')) {
                return redirect()->intended(route('guru.dashboard'));
            }
            
            return redirect()->intended(route('dashboard'));
        }

        \Illuminate\Support\Facades\RateLimiter::hit($kunci, 60);
        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
