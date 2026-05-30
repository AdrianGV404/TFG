<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterPersonal extends Component
{
    public $name;
    public $email;
    public $password;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ];

    public function register()
    {
        $this->validate();

        // Crear un tenant nuevo (el ID será autoincremental)
        $tenant = Tenant::create([
            'name' => $this->name . ' (Personal)',
            'type' => 'personal',
        ]);

        // Crear usuario asociado a ese tenant
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'tenant_id' => $tenant->id, // aquí usamos el ID autoincremental
        ]);

        Auth::login($user);

        return redirect()->route('projects');
    }

    public function render()
    {
        return view('livewire.register-personal');
    }
}
