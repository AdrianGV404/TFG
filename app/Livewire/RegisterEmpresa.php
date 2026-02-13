<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterEmpresa extends Component
{
    public $name;
    public $email;
    public $password;
    public $companyName;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'companyName' => 'required|string|max:255',
    ];

    public function register()
    {
        $this->validate();

        // Crear un nuevo tenant de tipo empresa
        $tenant = Tenant::create([
            'name' => $this->companyName,
            'type' => 'empresa',
        ]);

        // Crear usuario admin asignado a este tenant
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'tenant_id' => $tenant->id,
            'role' => 'admin', // admin de empresa
        ]);

        Auth::login($user);

        return redirect()->route('projects');
    }

    public function render()
    {
        return view('livewire.register-empresa');
    }
}
