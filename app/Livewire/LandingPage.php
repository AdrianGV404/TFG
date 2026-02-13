<?php

namespace App\Livewire;

use Livewire\Component;

class Landingpage extends Component
{
    public $showOptions = false;

    // Toggle mostrar las opciones de entorno
    public function toggleOptions()
    {
        $this->showOptions = !$this->showOptions;
    }

    // Crear un tenant personal → redirige a register personal
    public function createPersonalTenant()
    {
        return redirect()->route('register.personal');
    }

    // Crear un tenant empresa → redirige a una ruta ejemplo
    public function createEmpresaTenant()
    {
        return redirect()->route('register.empresa');
    }

    public function render()
    {
        return view('livewire.landing-page');
    }
}
