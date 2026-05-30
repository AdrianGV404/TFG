<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Login extends Component
{
    public $email;
    public $password;

    public function login()
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            session()->regenerate();
            return redirect()->intended('/projects');
        }

        throw ValidationException::withMessages([
            'email' => 'Correo o contraseña incorrecta',
        ]);
    }

    public function render()
    {
        return view('livewire.login');
    }
}
