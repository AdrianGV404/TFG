<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserManagement extends Component
{
    public $editingUserId = null;
    public $editingName = '';
    public $editingEmail = '';
    public $editingPassword = '';

    // Array para almacenar temporalmente los permisos del usuario en edición
    public $editingPermissions = [
        'can_create_projects' => false,
        'can_create_project_tasks_by_others' => false,
        'can_create_any_task' => false,
        'can_edit_project_tasks_by_others' => false,
        'can_edit_any_task' => false,
        'can_delete_project_tasks_by_others' => false,
        'can_delete_any_task' => false,
        'can_reassign_users' => false,
    ];

    protected $rules = [
        'editingName' => 'required|string|max:255',
        'editingEmail' => 'required|email',
        'editingPassword' => 'nullable|min:6',
        'editingPermissions.can_create_projects' => 'boolean',
        'editingPermissions.can_create_project_tasks_by_others' => 'boolean',
        'editingPermissions.can_create_any_task' => 'boolean',
        'editingPermissions.can_edit_project_tasks_by_others' => 'boolean',
        'editingPermissions.can_edit_any_task' => 'boolean',
        'editingPermissions.can_delete_project_tasks_by_others' => 'boolean',
        'editingPermissions.can_delete_any_task' => 'boolean',
        'editingPermissions.can_reassign_users' => 'boolean',
    ];

    public function mount()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }
    }

    public function startEdit($id)
    {
        // Traemos al usuario con sus permisos cargados (Eager Loading)
        $user = User::with('customPermissions')->findOrFail($id);

        $this->editingUserId = $user->id;
        $this->editingName = $user->name;
        $this->editingEmail = $user->email;
        $this->editingPassword = '';

        // Si el usuario ya tiene fila de permisos, la cargamos; si no, dejamos todo en falso por defecto
        $perms = $user->customPermissions;
        $this->editingPermissions = [
            'can_create_projects' => $perms ? $perms->can_create_projects : false,
            'can_create_project_tasks_by_others' => $perms ? $perms->can_create_project_tasks_by_others : false,
            'can_create_any_task' => $perms ? $perms->can_create_any_task : false,
            'can_edit_project_tasks_by_others' => $perms ? $perms->can_edit_project_tasks_by_others : false,
            'can_edit_any_task' => $perms ? $perms->can_edit_any_task : false,
            'can_delete_project_tasks_by_others' => $perms ? $perms->can_delete_project_tasks_by_others : false,
            'can_delete_any_task' => $perms ? $perms->can_delete_any_task : false,
            'can_reassign_users' => $perms ? $perms->can_reassign_users : false,
        ];
    }

    public function cancelEdit()
    {
        $this->reset([
            'editingUserId',
            'editingName',
            'editingEmail',
            'editingPassword',
            'editingPermissions'
        ]);
    }

    public function saveEdit()
    {
        $this->validate();

        $user = User::findOrFail($this->editingUserId);

        $user->name = $this->editingName;
        $user->email = $this->editingEmail;

        if (!empty($this->editingPassword)) {
            $user->password = Hash::make($this->editingPassword);
        }

        $user->save();

        // Guardamos o actualizamos los permisos en la tabla secundaria
        $user->customPermissions()->updateOrCreate(
            ['user_id' => $user->id],
            $this->editingPermissions
        );

        $this->cancelEdit();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Usuario y permisos actualizados con éxito"
        ]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            $this->dispatch('notify', [
                'type' => 'danger',
                'message' => "No puedes eliminarte a ti mismo"
            ]);
            return;
        }

        $user->delete();

        $this->dispatch('notify', [
            'type' => 'danger',
            'message' => "Usuario eliminado"
        ]);
    }

    public function render()
    {
        // Cargamos los usuarios vinculando sus permisos para optimizar consultas a la BD
        $users = User::with('customPermissions')
                     ->where('tenant_id', Auth::user()->tenant_id)
                     ->get();

        return view('livewire.user-management', [
            'users' => $users
        ]);
    }
}