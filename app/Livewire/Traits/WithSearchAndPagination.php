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
    public string $orderBy = 'id_desc';

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
    protected function applyFilters(Builder $query, string $textColumn, ?string $statusOrder = null): LengthAwarePaginator
    {
        $searchText = trim((string) $this->searchText);
        $searchId = trim((string) $this->searchId);

        $query->when($searchText !== '', fn (Builder $q) => $q->where($textColumn, 'like', "%{$searchText}%"));

        $query->when($searchId !== '', fn (Builder $q) => $q->where('id', 'like', "{$searchId}%"));

        match ($this->orderBy) {
            'id_asc' => $query->orderBy('id', 'asc'),
            'status' => ($statusOrder)
                ? $query->orderByRaw($statusOrder)
                : $query->orderBy('status'),
            default => $query->orderByDesc('id'),
        };

        $perPage = $this->perPage > 0 ? $this->perPage : 10;

        return $query->paginate($perPage);
    }
}
