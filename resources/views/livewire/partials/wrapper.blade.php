@extends('layouts.app')

@section('content')
    @if(isset($component))
        @if(isset($project))
            {{-- Componente con proyecto --}}
            @livewire($component, ['project' => $project])
        @else
            {{-- Componente sin proyecto --}}
            @livewire($component)
        @endif
    @endif
@endsection
