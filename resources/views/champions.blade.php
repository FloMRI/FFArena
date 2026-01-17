@foreach($champions as $champion)
    <h1>{{ $champion->name }} - {{ $champion->imagePath }} -
        @foreach($champion->tags as $tag)
            {{ $tag }}
        @endforeach
    </h1>
@endforeach
