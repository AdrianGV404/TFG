@php
    $total = $total ?? 0;
    $done = $done ?? 0;
    $inProgress = $inProgress ?? 0;
    $pending = $pending ?? 0;

    $donePct = $total > 0 ? round($done * 100 / $total) : 0;
    $inProgressPct = $total > 0 ? round($inProgress * 100 / $total) : 0;
    $pendingPct = $total > 0 ? round($pending * 100 / $total) : 0;
@endphp

<div class="project-stats">
    <div class="project-progress">
        <div class="project-progress-bar done" style="width: {{ $donePct }}%;"></div>
        <div class="project-progress-bar in_progress" style="width: {{ $inProgressPct }}%;"></div>
        <div class="project-progress-bar pending" style="width: {{ $pendingPct }}%;"></div>
    </div>

    <small class="text-muted">
        ✔ {{ $done }} ({{ $donePct }}%) &nbsp;&nbsp;
        ⏳ {{ $inProgress }} ({{ $inProgressPct }}%) &nbsp;&nbsp;
        ⏺ {{ $pending }} ({{ $pendingPct }}%)
    </small>
</div>
