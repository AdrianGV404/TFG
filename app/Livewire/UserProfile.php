<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;

class UserProfile extends Component
{
    use WithFileUploads;

    public $name, $email, $photo;
    public $profile_photo_path;
    public $current_password, $new_password, $new_password_confirmation;

    // Preferencias
    public $theme;
    public $notif_tasks;
    public $notif_alerts;
    public $notif_reports;
    public $notif_status_change;
    public $notif_channel;
    public $retention_days;
    public $allow_employee_tags;

    public function mount(): void
    {
        $user     = auth()->user();
        $settings = $user->settings;

        $this->name               = $user->name;
        $this->email              = $user->email;
        $this->profile_photo_path = $user->profile_photo_path;

        $this->theme               = $settings->theme               ?? 'light';
        $this->notif_tasks         = $settings->notif_tasks         ?? true;
        $this->notif_alerts        = $settings->notif_alerts        ?? true;
        $this->notif_reports       = $settings->notif_reports       ?? true;
        $this->notif_status_change = $settings->notif_status_change ?? true;
        $this->notif_channel       = $settings->notif_channel       ?? 'app';
        $this->retention_days      = $settings->retention_days      ?? 90;
        $this->allow_employee_tags = $settings->allow_employee_tags ?? true;
    }

    public function saveAll(): void
    {
        $user = auth()->user();

        $this->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $user->id,
            'photo'          => 'nullable|image|max:1024',
            'retention_days' => 'required|numeric|min:1|max:365',
            'notif_channel'  => 'required|in:app,email,both',
        ]);

        // 1. Datos del usuario
        $user->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        if ($this->photo) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $this->photo->store('profile-photos', 'public');
            $user->update(['profile_photo_path' => $path]);
        }

        // 2. Configuración
        $user->settings()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'theme'               => $this->theme,
                'notif_tasks'         => $this->notif_tasks,
                'notif_alerts'        => $this->notif_alerts,
                'notif_reports'       => $this->notif_reports,
                'notif_status_change' => $this->notif_status_change,
                'notif_channel'       => $this->notif_channel,
                'retention_days'      => $this->retention_days,
                'allow_employee_tags' => $this->allow_employee_tags,
            ]
        );

        // 3. Refrescar
        $user->load('settings');
        $this->profile_photo_path = $user->profile_photo_path;
        $this->reset('photo');

        $this->dispatch('theme-updated', theme: $this->theme);
        $this->dispatch('notify', message: '¡Configuración guardada!', type: 'success');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password'     => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch('notify', message: 'Contraseña cambiada con éxito.', type: 'success');
    }

    public function render()
    {
        return view('livewire.user-profile');
    }
}