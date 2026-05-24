<?php

namespace App\Livewire;

use App\Models\Notification;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $open        = false;
    public int  $unreadCount = 0;
    public $notifications    = [];

    // Refresca automáticamente cada 10 segundos (wire:poll en la vista)
    public function loadNotifications(): void
    {
        $user = auth()->user();

        $this->notifications = Notification::where('user_id', $user->id)
            ->latest()
            ->take(12)
            ->get();

        $this->unreadCount = Notification::where('user_id', $user->id)
            ->unread()
            ->count();
    }

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function markAsRead(int $id): void
    {
        Notification::where('user_id', auth()->id())
            ->where('id', $id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadNotifications();
    }

    public function markAllAsRead(): void
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}