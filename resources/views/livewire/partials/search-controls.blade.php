<div class="search-controls">

    {{-- BUSCADORES --}}
    <div class="search-left">
        <input
            type="text"
            class="form-control form-control-sm search-input-small"
            placeholder="{{ $textPlaceholder }}"
            wire:model.live.debounce.400ms="searchText"
        >

        <input
            type="text"
            class="form-control form-control-sm search-input-id"
            placeholder="ID"
            wire:model.live="searchId"
        >
    </div>

    {{-- ORDEN Y PAGINACIÓN --}}
    <div class="search-right">
        <select
            class="form-select search-select-wide"
            wire:model.live="orderBy"
        >
            <option value="id_desc">ID ↓ (más recientes)</option>
            <option value="id_asc">ID ↑ (más antiguos)</option>

            @if ($allowStatusOrder)
                <option value="status">Estado</option>
            @endif
        </select>

        <select
            class="form-select form-select-sm"
            wire:model.live="perPage"
        >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>
    </div>

</div>
