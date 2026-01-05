<?php

namespace App\Livewire\Traits;

trait ScopedByProject
{
    /**
     * Return a query builder scoped to the current component project.
     * Expects the component to have a `Project $project` property.
     *
     * @param string $modelClass
     */
    protected function scopedQuery(string $modelClass)
    {
        return $modelClass::where('project_id', $this->project->id);
    }

    protected function findScoped(string $modelClass, int $id)
    {
        return $this->scopedQuery($modelClass)->where('id', $id)->firstOrFail();
    }

    protected function updateScoped(string $modelClass, int $id, array $data): int
    {
        return $this->scopedQuery($modelClass)->where('id', $id)->update($data);
    }

    protected function deleteScoped(string $modelClass, int $id): int
    {
        return $this->scopedQuery($modelClass)->where('id', $id)->delete();
    }

    /**
     * Create a new model instance scoped to the current project.
     * Ensures `project_id` is always set from the component's `Project $project`.
     *
     * @param string $modelClass
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function createScoped(string $modelClass, array $data)
    {
        $data['project_id'] = $this->project->id;
        return $modelClass::create($data);
    }
}
