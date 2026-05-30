<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Label;
use App\Livewire\Traits\Notifies;
use Livewire\Attributes\On;

class LabelCreateModal extends Component
{
    use Notifies;

    public bool $showModal = false;
    public string $name = '';
    
    // Nueva variable para saber si estamos editando
    public ?int $editId = null; 

    #[On('open-label-modal')]
    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    // Método de apoyo para limpiar el formulario
    public function resetForm()
    {
        $this->reset(['name', 'editId']);
        $this->resetValidation();
    }

    public function editLabel($id)
    {
        $tenantId = auth()->user()->tenant_id;
        
        // Buscamos la etiqueta asegurando que pertenece a este tenant
        $label = Label::where('tenant_id', $tenantId)->findOrFail($id);
        
        $this->editId = $label->id;
        $this->name = $label->name;
        $this->resetValidation();
    }

    public function deleteLabel($id)
    {
        $tenantId = auth()->user()->tenant_id;
        
        $label = Label::where('tenant_id', $tenantId)->findOrFail($id);
        $labelName = $label->name;
        $label->delete();

        $this->notify("Etiqueta \"{$labelName}\" eliminada", 'success');
        
        // Si estábamos editando la etiqueta que acabamos de borrar, limpiamos el formulario
        if ($this->editId === $id) {
            $this->resetForm();
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'El nombre de la etiqueta es obligatorio.',
        ]);

        $tenantId = auth()->user()->tenant_id;

        // Verificar si existe otra etiqueta con este nombre, ignorando la que estamos editando
        $query = Label::where('tenant_id', $tenantId)->where('name', $this->name);
        if ($this->editId) {
            $query->where('id', '!=', $this->editId);
        }

        if ($query->exists()) {
            $this->addError('name', 'Ya existe una etiqueta con este nombre en tu espacio.');
            return;
        }

        // Determinar si actualizamos o creamos
        if ($this->editId) {
            $label = Label::where('tenant_id', $tenantId)->findOrFail($this->editId);
            $label->update(['name' => $this->name]);
            $this->notify("Etiqueta \"{$this->name}\" actualizada", 'success');
        } else {
            Label::create([
                'tenant_id' => $tenantId,
                'name' => $this->name,
            ]);
            $this->notify("Etiqueta \"{$this->name}\" creada con éxito", 'success');
        }

        // Limpiamos el form para seguir gestionando, pero NO cerramos el modal
        // para que el usuario pueda ver la lista actualizada al instante.
        $this->resetForm();
    }

    public function render()
    {
        // Traemos todas las etiquetas del Tenant actual para listarlas
        $labels = Label::where('tenant_id', auth()->user()->tenant_id)
                       ->orderBy('name')
                       ->get();

        return view('livewire.label-create-modal', [
            'labels' => $labels
        ]);
    }
}