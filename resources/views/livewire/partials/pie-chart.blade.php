@php
    $total = $total ?? 0;
    $done = $done ?? 0;
    $inProgress = $inProgress ?? 0;
    $pending = $pending ?? 0;

    $donePct = $total > 0 ? ($done / $total) * 100 : 0;
    $inProgressPct = $total > 0 ? ($inProgress / $total) * 100 : 0;
    $pendingPct = $total > 0 ? max(0, 100 - $donePct - $inProgressPct) : 0;

    // Convert percentages a grados (360°)
    $doneDeg = ($donePct / 100) * 360;
    $inProgressDeg = ($inProgressPct / 100) * 360;
    $pendingDeg = ($pendingPct / 100) * 360;

    $radius = 50;
    $center = 50;

    // Use closures to avoid global function redeclaration when the partial is included multiple times.
    $polarToCartesian = function ($centerX, $centerY, $radius, $angleInDegrees) {
        $angleInRadians = ($angleInDegrees - 90) * pi() / 180.0;

        return [
            'x' => $centerX + ($radius * cos($angleInRadians)),
            'y' => $centerY + ($radius * sin($angleInRadians)),
        ];
    };

    $describeArc = function ($x, $y, $radius, $startAngle, $endAngle) use ($polarToCartesian) {
        $start = $polarToCartesian($x, $y, $radius, $endAngle);
        $end = $polarToCartesian($x, $y, $radius, $startAngle);
        $largeArcFlag = (($endAngle - $startAngle) <= 180) ? "0" : "1";

        return "M{$x},{$y} L{$start['x']},{$start['y']} A{$radius},{$radius} 0 $largeArcFlag,0 {$end['x']},{$end['y']} Z";
    };

    // Prepare paths or flags to draw full circles when a segment represents 100%
    $epsilon = 0.0001;
    $doneIsFull = ($doneDeg >= 360 - $epsilon);
    $inProgressIsFull = ($inProgressDeg >= 360 - $epsilon);
    $pendingIsFull = ($pendingDeg >= 360 - $epsilon);

    $donePath = !$doneIsFull && ($doneDeg > $epsilon) ? $describeArc($center, $center, $radius, 0, $doneDeg) : null;
    $inProgressPath = !$inProgressIsFull && ($inProgressDeg > $epsilon) ? $describeArc($center, $center, $radius, $doneDeg, $doneDeg + $inProgressDeg) : null;
    $pendingPath = !$pendingIsFull && ($pendingDeg > $epsilon) ? $describeArc($center, $center, $radius, $doneDeg + $inProgressDeg, 360) : null;
@endphp

<div class="piecol">
    <svg width="100" height="100" viewBox="0 0 100 100" aria-hidden="true">
        {{-- Done --}}
        @if($done > 0)
            @if($doneIsFull)
                <circle cx="{{ $center }}" cy="{{ $center }}" r="{{ $radius }}" fill="#4caf7a" />
            @elseif($donePath)
                <path d="{{ $donePath }}" fill="#4caf7a"/>
            @endif
        @endif

        {{-- In Progress --}}
        @if($inProgress > 0)
            @if($inProgressIsFull)
                <circle cx="{{ $center }}" cy="{{ $center }}" r="{{ $radius }}" fill="#3399ff" />
            @elseif($inProgressPath)
                <path d="{{ $inProgressPath }}" fill="#3399ff"/>
            @endif
        @endif

        {{-- Pending --}}
        @if($pending > 0)
            @if($pendingIsFull)
                <circle cx="{{ $center }}" cy="{{ $center }}" r="{{ $radius }}" fill="#ffc107" />
            @elseif($pendingPath)
                <path d="{{ $pendingPath }}" fill="#ffc107"/>
            @endif
        @endif
    </svg>

    <div class="tasks-legend">
        <div>✔ <strong style="color:#2e7d32">{{ $done }} ({{ round($donePct) }}%)</strong></div>
        <div>⏳ <strong style="color:#3399ff">{{ $inProgress }} ({{ round($inProgressPct) }}%)</strong></div>
        <div>⏺ <strong style="color:#ffc107">{{ $pending }} ({{ round(max(0, $pendingPct)) }}%)</strong></div>
    </div>
</div>
