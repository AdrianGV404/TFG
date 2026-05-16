<div>

    {{-- ASIGNADOS --}}
    <h6>Usuarios asignados</h6>

    @foreach($this->users as $user)
        <span class="badge bg-secondary">
            {{ $user->name }}

            <button wire:click="removeUser({{ $user->id }})">x</button>
        </span>
    @endforeach

    <hr>

    {{-- BUSCADOR --}}
    <input type="text" wire:model.live="search" placeholder="Buscar usuarios...">

    {{-- RESULTADOS --}}
    @foreach($this->availableUsers as $user)
        <div class="d-flex justify-content-between">
            <span>{{ $user->name }}</span>

            <button wire:click="addUser({{ $user->id }})">
                +
            </button>
        </div>
    @endforeach

</div>