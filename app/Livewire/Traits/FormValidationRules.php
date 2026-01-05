<?php

namespace App\Livewire\Traits;

trait FormValidationRules
{
    /**
     * Validation rules for Project create/edit forms.
     */
    protected function projectRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,archived',
        ];
    }

    /**
     * Validation rules for inline project editing fields (editingName, ...)
     */
    protected function projectRulesForEditing(string $prefix = 'editing'): array
    {
        return [
            "{$prefix}Name" => 'required|string|max:255',
            "{$prefix}Description" => 'nullable|string',
            "{$prefix}Status" => 'required|in:active,archived',
        ];
    }

    /**
     * Validation rules for Task create/edit forms.
     */
    protected function taskRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,done',
        ];
    }

    /**
     * Validation rules for inline task editing fields (editingTitle, ...)
     */
    protected function taskRulesForEditing(string $prefix = 'editing'): array
    {
        return [
            "{$prefix}Title" => 'required|string|max:255',
            "{$prefix}Description" => 'nullable|string',
        ];
    }
}
