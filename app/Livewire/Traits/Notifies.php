<?php

namespace App\Livewire\Traits;

trait Notifies
{
    /**
     * Dispatch a standardized notify event for the frontend.
     *
     * @param string $message
     * @param string $type bootstrap alert type (e.g. 'success','danger','info')
     * @return void
     */
    protected function notify(string $message, string $type = 'info'): void
    {
        $this->dispatch('notify', message: $message, type: $type);
    }
}
