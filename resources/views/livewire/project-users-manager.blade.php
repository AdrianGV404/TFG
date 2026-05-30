<div>
    <style>
        .pum-wrap { display: flex; flex-direction: column; gap: 10px; }

        /* Chips de usuarios asignados */
        .pum-assigned { display: flex; flex-wrap: wrap; gap: 6px; }
        .pum-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--pr-card, #fff);
            border: 1.5px solid var(--pr-border, #e4e9f0);
            border-radius: 20px;
            padding: 3px 8px 3px 4px;
            font-size: .75rem;
            font-weight: 500;
            color: var(--pr-text, #0f172a);
            transition: border-color .15s;
        }
        .pum-chip:hover { border-color: #ef4444; }
        .pum-chip-avatar {
            width: 22px; height: 22px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .6rem; font-weight: 700; color: #fff;
            flex-shrink: 0; text-transform: uppercase; overflow: hidden;
        }
        .pum-chip-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        .pum-chip-remove {
            background: none; border: none; cursor: pointer;
            color: #94a3b8; font-size: .75rem; padding: 0;
            line-height: 1; display: flex; align-items: center; transition: color .12s;
        }
        .pum-chip-remove:hover { color: #ef4444; }
        .pum-empty-team { font-size: .75rem; color: #94a3b8; font-style: italic; }

        /* Buscador */
        .pum-search-wrap { position: relative; }
        .pum-search-input {
            width: 100%; padding: 7px 10px 7px 30px;
            border: 1.5px solid var(--pr-border, #e4e9f0);
            border-radius: 8px; background: var(--pr-bg, #f0f4f9);
            color: var(--pr-text, #0f172a); font-size: .8rem;
            outline: none; transition: border-color .15s; box-sizing: border-box;
        }
        .pum-search-input:focus { border-color: var(--pr-accent, #3b6ef6); }
        .pum-search-icon {
            position: absolute; left: 9px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: .75rem; pointer-events: none;
        }

        /* Lista de disponibles */
        .pum-results {
            display: flex; flex-direction: column; gap: 2px;
            max-height: 180px; overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--pr-border, #e4e9f0) transparent;
            border: 1.5px solid var(--pr-border, #e4e9f0);
            border-radius: 9px; padding: 4px;
            background: var(--pr-bg, #f0f4f9);
        }
        .pum-results::-webkit-scrollbar { width: 4px; }
        .pum-results::-webkit-scrollbar-thumb { background: var(--pr-border, #e4e9f0); border-radius: 2px; }

        .pum-result-row {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 8px; border-radius: 7px;
            cursor: pointer; transition: background .12s;
            border: none; background: none; width: 100%; text-align: left;
        }
        .pum-result-row:hover { background: rgba(59,110,246,.09); }
        .pum-result-avatar {
            width: 26px; height: 26px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .62rem; font-weight: 700; color: #fff;
            flex-shrink: 0; text-transform: uppercase; overflow: hidden;
        }
        .pum-result-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        .pum-result-name {
            font-size: .8rem; font-weight: 500;
            color: var(--pr-text, #0f172a);
            flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .pum-result-add {
            font-size: .7rem; color: var(--pr-accent, #3b6ef6);
            font-weight: 700; flex-shrink: 0;
            opacity: 0; transition: opacity .12s;
            display: flex; align-items: center; gap: 3px;
        }
        .pum-result-row:hover .pum-result-add { opacity: 1; }
        .pum-no-results { font-size: .75rem; color: #94a3b8; padding: 8px 10px; text-align: center; }
        .pum-section-label {
            font-size: .68rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: .05em; color: #94a3b8; padding: 2px 6px 4px;
        }
    </style>

    <div class="pum-wrap">

        {{-- Usuarios ya asignados --}}
        <div class="pum-assigned">
            @forelse ($this->users as $user)
                @php
                    $colors   = ['#3b6ef6','#6366f1','#22c55e','#f59e0b','#ef4444','#06b6d4','#8b5cf6','#ec4899'];
                    $bg       = $colors[$user->id % count($colors)];
                    $initials = collect(explode(' ', $user->name))->map(fn($p) => strtoupper(substr($p,0,1)))->take(2)->implode('');
                @endphp
                <div class="pum-chip" title="{{ $user->name }}">
                    <div class="pum-chip-avatar" style="background:{{ $bg }};">
                        @if ($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}">
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <span>{{ $user->name }}</span>
                    <button type="button" class="pum-chip-remove" wire:click="removeUser({{ $user->id }})" title="Quitar del proyecto">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @empty
                <span class="pum-empty-team">Sin usuarios asignados</span>
            @endforelse
        </div>

        {{-- Buscador --}}
        <div class="pum-search-wrap">
            <i class="fas fa-search pum-search-icon"></i>
            <input
                type="text"
                class="pum-search-input"
                placeholder="Buscar usuario para añadir..."
                wire:model.live.debounce.250ms="search"
            >
        </div>

        {{-- Lista de disponibles (siempre visible) --}}
        @php $available = $this->availableUsers; @endphp
        <div class="pum-results" wire:loading.class="opacity-50" wire:target="search,addUser,removeUser">
            @if ($available->isEmpty())
                <div class="pum-no-results">
                    @if (trim($search) !== '')
                        No se encontraron usuarios con "{{ $search }}"
                    @else
                        Todos los miembros del tenant ya están asignados
                    @endif
                </div>
            @else
                <div class="pum-section-label">
                    {{ trim($search) !== '' ? 'Resultados' : 'Disponibles para añadir' }}
                    <span style="font-weight:400;">({{ $available->count() }})</span>
                </div>
                @foreach ($available as $user)
                    @php
                        $colors   = ['#3b6ef6','#6366f1','#22c55e','#f59e0b','#ef4444','#06b6d4','#8b5cf6','#ec4899'];
                        $bg       = $colors[$user->id % count($colors)];
                        $initials = collect(explode(' ', $user->name))->map(fn($p) => strtoupper(substr($p,0,1)))->take(2)->implode('');
                    @endphp
                    <button type="button" class="pum-result-row" wire:click="addUser({{ $user->id }})" wire:key="avail-{{ $user->id }}">
                        <div class="pum-result-avatar" style="background:{{ $bg }};">
                            @if ($user->profile_photo_path)
                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}">
                            @else
                                {{ $initials }}
                            @endif
                        </div>
                        <span class="pum-result-name">{{ $user->name }}</span>
                        <span class="pum-result-add"><i class="fas fa-plus"></i> Añadir</span>
                    </button>
                @endforeach
            @endif
        </div>

    </div>
</div>