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

    // Escucha el evento global para abrir el modal desde cualquier sitio
    #[On('open-label-modal')]
    public function openModal()
    {
        $this->reset('name');
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'El nombre de la etiqueta es obligatorio.',
        ]);

        $tenantId = auth()->user()->tenant_id;

        // Verificar si la etiqueta ya existe en este tenant para no duplicarla
        $exists = Label::where('tenant_id', $tenantId)
                       ->where('name', $this->name)
                       ->exists();

        if ($exists) {
            $this->addError('name', 'Ya existe una etiqueta con este nombre en tu espacio.');
            return;
        }

        // Crear la etiqueta
        Label::create([
            'tenant_id' => $tenantId,
            'name' => $this->name,
        ]);

        // Lanzar tu notificación
        $this->notify("Etiqueta \"{$this->name}\" creada con éxito", 'success');
        
        // Cerrar el modal
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.label-create-modal');
    }
}