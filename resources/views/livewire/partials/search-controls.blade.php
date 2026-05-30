@php
    $isProjectList = $isProjectList ?? false;
    $allowStatusOrder = $allowStatusOrder ?? false;
    $textPlaceholder = $textPlaceholder ?? '';
    $labels = $labels ?? [];
@endphp

<div class="search-controls"
    style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: space-between; align-items: center;">

    {{-- BUSCADORES Y FILTROS --}}
    <div class="search-left" style="display: flex; gap: 0.5rem; align-items: center;">
        <input type="text" class="form-control form-control-sm search-input-small" placeholder="{{ $textPlaceholder }}"
            wire:model.live.debounce.400ms="searchText">

        <input type="text" class="form-control form-control-sm search-input-id" placeholder="ID"
            wire:model.live="searchId" style="max-width: 80px;">

        {{-- NUEVO: DESPLEGABLE DE ETIQUETAS --}}
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="filterLabelsDropdown"
                data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                Filtrar Etiquetas
            </button>
            <ul class="dropdown-menu p-2 shadow" aria-labelledby="filterLabelsDropdown"
                style="max-height: 250px; overflow-y: auto; min-width: 200px;">
                @forelse($labels as $label)
                    <li>
                        <div class="form-check m-1">
                            <input class="form-check-input" type="checkbox" value="{{ $label->id }}"
                                id="filter-label-{{ $label->id }}" wire:model.live="searchLabels">
                            <label class="form-check-label" for="filter-label-{{ $label->id }}">
                                {{ $label->name }}
                            </label>
                        </div>
                    </li>
                @empty
                    <li><span class="dropdown-item-text text-muted small">No hay etiquetas disponibles.</span></li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- ORDEN Y PAGINACIÓN --}}
    <div class="search-right" style="display: flex; gap: 0.5rem; align-items: center;">
        <select class="form-select form-select-sm search-select-wide" wire:model.live="orderBy">
            {{-- Mostrar prioridad SOLO si NO es lista de proyectos --}}
            @if (!$isProjectList)
                <option value="priority">Prioridad</option>
            @endif

            {{-- Mostrar estado si se permite --}}
            @if ($allowStatusOrder)
                <option value="status">Estado</option>
            @endif

            <option value="id_desc">ID ↓ (más recientes)</option>
            <option value="id_asc">ID ↑ (más antiguos)</option>
        </select>

        <select class="form-select form-select-sm" wire:model.live="perPage" style="max-width: 80px;">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>
</div>
