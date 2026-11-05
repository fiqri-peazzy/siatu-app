<?php

namespace App\Livewire\Mobile\Guru;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DaftarNotifikasi extends Component
{
    public function mount()
    {
        // Mark all as read when opening notification page
        if (Auth::user()) {
            Auth::user()->unreadNotifications->markAsRead();
        }
    }

    public function render()
    {
        $notifications = Auth::user() ? Auth::user()->notifications()->paginate(15) : [];
        
        return view('livewire.mobile.guru.daftar-notifikasi', [
            'notifications' => $notifications
        ])->layout('components.layouts.pwa', ['judul' => 'Notifikasi']);
    }
}
