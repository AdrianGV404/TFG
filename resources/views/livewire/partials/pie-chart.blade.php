@php
    /**
     * Variables que deben pasarse al incluir el partial:
     * - $labels: array con los nombres de las secciones ['Pendiente','En progreso','Hecha']
     * - $values: array con los valores numéricos [10,5,2]
     * - $colors: array con los colores ['#ffc107','#0d6efd','#198754']
     */

    $total = array_sum($values);
    $angles = [];
    $startAngle = 0;

    foreach ($values as $val) {
        $pct = $total > 0 ? ($val / $total) * 100 : 0;
        $angles[] = ($pct / 100) * 360;
    }

    $radius = 50;
    $center = 50;

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
@endphp

<div class="piecol">
    <svg width="100" height="100" viewBox="0 0 100 100" aria-hidden="true">
        @php $currentAngle = 0; @endphp
        @foreach($values as $i => $val)
            @php
                $deg = $angles[$i];
                $isFull = $deg >= 360 - 0.0001;
                $path = !$isFull && $deg > 0 ? $describeArc($center, $center, $radius, $currentAngle, $currentAngle + $deg) : null;
            @endphp

            @if($val > 0)
                @if($isFull)
                    <circle cx="{{ $center }}" cy="{{ $center }}" r="{{ $radius }}" fill="{{ $colors[$i] }}" />
                @elseif($path)
                    <path d="{{ $path }}" fill="{{ $colors[$i] }}" />
                @endif
            @endif

            @php $currentAngle += $deg; @endphp
        @endforeach
    </svg>

    <div class="tasks-legend">
        @foreach($values as $i => $val)
            @php $pct = $total > 0 ? ($val / $total) * 100 : 0; @endphp
            <div><strong style="color:{{ $colors[$i] }}">{{ $labels[$i] }}: {{ $val }} ({{ round($pct) }}%)</strong></div>
        @endforeach
    </div>
</div>
