
<form method="POST" action="/champions">
    @csrf

    <input id="search" name="search" type="text">

    <button type="submit">Submit</button>
</form>


@foreach($champions as $champion)
    <img src="{{ asset('storage/champion/'.$champion->imagePath) }}" alt="champion avatar">
    <h1>{{ $champion->name }} -
        @foreach($champion->tags as $tag)
            {{ $tag }}
        @endforeach
    </h1>
@endforeach
