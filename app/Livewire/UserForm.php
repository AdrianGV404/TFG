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

    // Propiedades para los nuevos permisos personalizables
    public $can_create_projects = false;
    public $can_create_project_tasks_by_others = false;
    public $can_create_any_task = false;
    public $can_edit_project_tasks_by_others = false;
    public $can_edit_any_task = false;
    public $can_delete_project_tasks_by_others = false;
    public $can_delete_any_task = false;
    public $can_reassign_users = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'can_create_projects' => 'boolean',
        'can_create_project_tasks_by_others' => 'boolean',
        'can_create_any_task' => 'boolean',
        'can_edit_project_tasks_by_others' => 'boolean',
        'can_edit_any_task' => 'boolean',
        'can_delete_project_tasks_by_others' => 'boolean',
        'can_delete_any_task' => 'boolean',
        'can_reassign_users' => 'boolean',
    ];

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
    }

    protected $layout = 'layouts.app';

    public function addUser()
    {
        $this->validate();

        // 1. Crear el usuario corporativo
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'tenant_id' => Auth::user()->tenant_id,
            'role' => 'user', 
        ]);

        // 2. Crear sus registros de permisos personalizados asignados en el formulario
        $user->customPermissions()->create([
            'can_create_projects' => $this->can_create_projects,
            'can_create_project_tasks_by_others' => $this->can_create_project_tasks_by_others,
            'can_create_any_task' => $this->can_create_any_task,
            'can_edit_project_tasks_by_others' => $this->can_edit_project_tasks_by_others,
            'can_edit_any_task' => $this->can_edit_any_task,
            'can_delete_project_tasks_by_others' => $this->can_delete_project_tasks_by_others,
            'can_delete_any_task' => $this->can_delete_any_task,
            'can_reassign_users' => $this->can_reassign_users,
        ]);

        // Limpiamos el formulario
        $this->reset();

        $this->notify("Usuario \"{$user->name}\" creado con éxito con sus permisos", 'success');

        return redirect()->route('projects');
    }
}