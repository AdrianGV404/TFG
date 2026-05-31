<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Tenant;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterPersonal extends Component
{
    public $name;
    public $email;
    public $password;

    // Permisos
    public $can_create_projects = false;
    public $can_create_project_tasks_by_others = false;
    public $can_create_any_task = false;
    public $can_edit_project_tasks_by_others = false;
    public $can_edit_any_task = false;
    public $can_delete_project_tasks_by_others = false;
    public $can_delete_any_task = false;
    public $can_reassign_users = false;

    protected $rules = [
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:6',
    ];

    public function addUser()
    {
        $this->validate();

        $tenant = Tenant::create([
            'name' => $this->name . ' (Personal)',
            'type' => 'personal',
        ]);

        $user = User::create([
            'name'      => $this->name,
            'email'     => $this->email,
            'password'  => Hash::make($this->password),
            'tenant_id' => $tenant->id,
        ]);

        UserPermission::create([
            'user_id'                           => $user->id,
            'can_create_projects'               => $this->can_create_projects,
            'can_create_project_tasks_by_others'=> $this->can_create_project_tasks_by_others,
            'can_create_any_task'               => $this->can_create_any_task,
            'can_edit_project_tasks_by_others'  => $this->can_edit_project_tasks_by_others,
            'can_edit_any_task'                 => $this->can_edit_any_task,
            'can_delete_project_tasks_by_others'=> $this->can_delete_project_tasks_by_others,
            'can_delete_any_task'               => $this->can_delete_any_task,
            'can_reassign_users'                => $this->can_reassign_users,
        ]);

        Auth::login($user);

        return redirect()->route('projects');
    }

    public function render()
    {
        return view('livewire.register-personal');
    }
}