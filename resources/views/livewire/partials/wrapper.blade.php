@extends('layouts.app')

@section('content')
    @if(isset($component))
        @if(isset($project))
            @livewire($component, ['project' => $project])
        @elseif(isset($task))
            @livewire($component, ['task' => $task])
        @else
            @livewire($component)
        @endif
    @endif
@endsection