@include('welcome')

<form method="POST" action="/">
    @csrf

    <input id="search" name="search" type="text">

    <button type="submit">Submit</button>
</form>


@foreach($champions as $champion)
    <div>
        <p title="{{ $champion->name }} - @foreach($champion->tags as $tag)@if($loop->last){{ $tag }}@else{{ $tag }}/@endif @endforeach">
            <img src="{{ asset('storage/champion/'.$champion->imagePath) }}" alt="champion avatar" width="128" height="128">
        </p>
    </div>
@endforeach
