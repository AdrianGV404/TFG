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

    protected $rules = [
        'editingName' => 'required|string|max:255',
        'editingEmail' => 'required|email',
        'editingPassword' => 'nullable|min:6',
    ];

    public function mount()
    {
        // Seguridad extra (aunque ya tienes middleware)
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403);
        }
    }

    public function startEdit($id)
    {
        $user = User::findOrFail($id);

        $this->editingUserId = $user->id;
        $this->editingName = $user->name;
        $this->editingEmail = $user->email;
        $this->editingPassword = '';
    }

    public function cancelEdit()
    {
        $this->reset([
            'editingUserId',
            'editingName',
            'editingEmail',
            'editingPassword'
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

        $this->cancelEdit();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Usuario actualizado con éxito"
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
        $users = User::where('tenant_id', Auth::user()->tenant_id)->get();

        return view('livewire.user-management', [
            'users' => $users
        ]);
    }
}
