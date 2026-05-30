<?php

namespace App\Livewire\Traits;

trait HasInlineEditing
{
    /**
     * Populate component editing properties from a model instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $idProperty component property that holds the id (e.g. 'editingTaskId')
     * @param array $map mapping of componentProperty => modelAttribute
     * @return void
     */
    protected function startEditingModel($model, string $idProperty, array $map): void
    {
        $this->{$idProperty} = $model->id;

        foreach ($map as $componentProp => $modelAttr) {
            $this->{$componentProp} = $model->{$modelAttr} ?? '';
        }
    }

    /**
     * Reset a group of component properties used for editing.
     *
     * @param array $props
     * @return void
     */
    protected function cancelEditing(array $props): void
    {
        $this->reset($props);
    }

    /**
     * Apply a simple update on a model class by id.
     * Returns number of affected rows.
     *
     * @param string $modelClass
     * @param int $id
     * @param array $data
     * @return int
     */
    protected function applyModelUpdate(string $modelClass, int $id, array $data): int
    {
        return $modelClass::where('id', $id)->update($data);
    }
}
