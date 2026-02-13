<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Traits\Notifies;

class UserForm extends Component
{
    use Notifies;
    public $name;
    public $email;
    public $password;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ];


    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
    }
    // Esto indica que Livewire debe usar tu layout
    protected $layout = 'layouts.app';

    public function addUser()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'tenant_id' => Auth::user()->tenant_id,
            'role' => 'user', // usuario normal
        ]);

        // Limpiamos el formulario
        $this->reset();

        // Mostramos notificación (puede ser un toast en front)
        //DESAPARECE SI SE USA REDIRECCIÓN, SI SE QUIERE USAR NOTIFICACIÓN, DEBE SER SIN REDIRECCIÓN
        $this->notify("Usuario \"{$user->name}\" creado con éxito", 'success');


        // Redirigimos a proyectos
        return redirect()->route('projects');
    }
}
