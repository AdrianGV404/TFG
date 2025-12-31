<div style="display:flex; justify-content:space-between; gap:12px; margin-bottom:12px; flex-wrap:wrap;">

    {{-- BUSCADORES --}}
    <div style="display:flex; gap:8px;">
        <input
            type="text"
            class="form-control form-control-sm"
            style="max-width:260px"
            placeholder="{{ $textPlaceholder }}"
            wire:model.live.debounce.400ms="searchText"
        >

        <input
            type="text"
            class="form-control form-control-sm"
            style="width:100px"
            placeholder="ID"
            wire:model.live="searchId"
        >
    </div>

    {{-- ORDEN Y PAGINACIÓN --}}
    <div style="display:flex; gap:10px;">
        <select
            class="form-select"
            style="min-width:190px;"
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
