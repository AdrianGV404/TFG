<!DOCTYPE html>
<html>

<head>
    <title>Posts externos</title>
</head>

<body>

    <h1>Posts desde API externa</h1>

    @if (session('error'))
        <p style="color:red">{{ session('error') }}</p>
    @endif

    <ul>
        @forelse ($posts as $post)
            <li>
                <strong>{{ $post['title'] }}</strong><br>
                {{ $post['body'] }}
            </li>
        @empty
            <li>No hay datos</li>
        @endforelse
    </ul>

</body>

</html>
