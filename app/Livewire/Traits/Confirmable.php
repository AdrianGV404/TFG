<?php

namespace App\Livewire\Traits;

trait Confirmable
{
    /**
     * Dispatch a standard confirm-delete event used by the frontend modal.
     *
     * @param string $title
     * @param string $message
     * @param string $action
     * @param int $id
     * @return void
     */
    protected function dispatchConfirmDelete(string $title, string $message, string $action, int $id): void
    {
        $this->dispatch(
            'confirm-delete',
            title: $title,
            message: $message,
            action: $action,
            id: $id
        );
    }
}
