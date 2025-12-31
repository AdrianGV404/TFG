<?php

namespace App\Livewire\Traits;

use Livewire\WithPagination;

trait WithSearchAndPagination
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $searchText = '';
    public string $searchId = '';
    public string $orderBy = 'id_desc';
    public int $perPage = 10;

    public function updatingSearchText() { $this->resetPage(); }
    public function updatingSearchId() { $this->resetPage(); }
    public function updatingOrderBy() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    protected function applyFilters($query, string $textColumn, ?string $statusOrder = null)
    {
        if ($this->searchText !== '') {
            $query->where($textColumn, 'like', '%' . $this->searchText . '%');
        }

        if ($this->searchId !== '') {
            $query->where('id', 'like', $this->searchId . '%');
        }

        match ($this->orderBy) {
            'id_asc' => $query->orderBy('id', 'asc'),
            'status' => $statusOrder
                ? $query->orderByRaw($statusOrder)
                : $query->orderBy('status'),
            default => $query->orderByDesc('id'),
        };

        return $query->paginate($this->perPage);
    }
}
