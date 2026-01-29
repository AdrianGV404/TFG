<?php

namespace App\Livewire\Traits;

use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait WithSearchAndPagination
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    /**
     * Free text search for a text column.
     */
    public string $searchText = '';

    /**
     * Prefix search for the id column.
     */
    public string $searchId = '';

    /**
     * Order by option. Supported: 'id_asc', 'status', 'id_desc' (default)
     */
    public string $orderBy = 'status';

    /**
     * Items per page for pagination. Must be positive.
     */
    public int $perPage = 10;

    public function updatingSearchText()
    {
        $this->resetPage();
    }

    public function updatingSearchId()
    {
        $this->resetPage();
    }

    public function updatingOrderBy()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Apply common search, id filter and ordering to a query and return paginated results.
     *
     * @param Builder $query
     * @param string $textColumn column name to search the free text against
     * @param string|null $statusOrder optional raw SQL used to order by status
     * @return LengthAwarePaginator
     */
    protected function applyFilters(Builder $query, string $textColumn, ?string $statusOrder = null, ?string $priorityOrder = null): LengthAwarePaginator
    {
        $searchText = trim((string) $this->searchText);
        $searchId = trim((string) $this->searchId);

        // Filtros de búsqueda
        $query->when($searchText !== '', fn (Builder $q) => $q->where($textColumn, 'like', "%{$searchText}%"));
        $query->when($searchId !== '', fn (Builder $q) => $q->where('id', 'like', "{$searchId}%"));

        // Ordenamiento
        match ($this->orderBy) {
            'id_asc' => $query->orderBy('id', 'asc'),

            'status' => $query->orderByRaw(
                "CASE
                    WHEN status = 'pending' THEN 1
                    WHEN status = 'in_progress' THEN 2
                    ELSE 3
                END"
            ),

            'priority' => $query->orderByRaw(
                "CASE
                    WHEN status = 'pending' AND priority = 'very_high' THEN 1
                    WHEN status = 'in_progress' AND priority = 'very_high' THEN 2
                    WHEN status = 'pending' AND priority = 'high' THEN 3
                    WHEN status = 'in_progress' AND priority = 'high' THEN 4
                    WHEN status = 'pending' AND priority = 'mid' THEN 5
                    WHEN status = 'in_progress' AND priority = 'mid' THEN 6
                    WHEN status = 'pending' AND priority = 'low' THEN 7
                    WHEN status = 'in_progress' AND priority = 'low' THEN 8
                    WHEN status = 'pending' AND priority = 'very_low' THEN 9
                    WHEN status = 'in_progress' AND priority = 'very_low' THEN 10
                    ELSE 11
                END"
            ),

            default => $query->orderByDesc('id'),
        };

        $perPage = $this->perPage > 0 ? $this->perPage : 10;

        return $query->paginate($perPage);
    }
}
