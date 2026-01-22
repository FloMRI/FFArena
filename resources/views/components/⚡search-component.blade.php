<?php

use App\Services\ChampionService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

new class extends Component {
    #[Validate('required|string|min:1')]
    public string $search = '';

    public function render(): Factory|View
    {
        $champions = [];

        if ($this->search && strlen($this->search) >= 1) {
            $champions = app(ChampionService::class)->search($this->search);
        }

        if ($this->search === '') {
            $champions = app(ChampionService::class)->getAll();
        }

        return view('components.⚡search-component', [
            'champions' => $champions
        ]);
    }
};
?>

<div>
    <input
        type="text"
        wire:model.live="search"
        placeholder="Search champions..."
        class="w-full px-4 py-2 border rounded"
    >

    <div class="mt-4">
        @if(count($champions) > 0)
            <div class="grid gap-4">
                @foreach($champions as $champion)
                    <div class="p-4 border rounded">
                        <h3 class="font-bold">{{ $champion->name }}</h3>
                        <img src="{{ asset('storage/champion/'.$champion->imagePath) }}" alt="{{ $champion->name }}">
                        <div class="flex gap-2 mt-2">
                            @foreach($champion->tags as $tag)
                                <span class="px-2 py-1 bg-gray-200 rounded text-sm">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No champions found.</p>
        @endif
    </div>
</div>
