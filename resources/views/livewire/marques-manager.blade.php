<div class="space-y-5 pb-20 lg:pb-0">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('app.marques') }}</h1>
            <p class="text-sm text-gray-500">{{ $this->marques->count() }} {{ __('app.aucune_marque') }}</p>
        </div>
        <button wire:click="nouveau"
            class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2.5 rounded-xl hover:bg-blue-700 text-sm">
            {{ __('app.nouvelle_marque') }}
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($this->marques as $m)
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-xl font-bold text-blue-600">
                    {{ strtoupper(substr($m->nom, 0, 1)) }}
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $m->actif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $m->actif ? __('app.active') : __('app.inactive') }}
                    </span>
                    <button wire:click="editer({{ $m->id }})"
                        class="text-xs text-blue-600 px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100">
                        {{ __('app.editer_btn') }}
                    </button>
                </div>
            </div>
            <p class="font-semibold text-gray-900">{{ $m->nom }}</p>
            @if($m->pays_origine)
            <p class="text-xs text-gray-400 mt-0.5">📍 {{ $m->pays_origine }}</p>
            @endif
            <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100">
                <p class="text-xs text-gray-400">{{ $m->produits_count }} {{ __('app.nb_produits') }}</p>
                @if($m->produits_count === 0)
                <button wire:click="supprimer({{ $m->id }})" wire:confirm="{{ __('app.supprimer_marque') }}"
                    class="text-xs text-red-400 hover:text-red-600">{{ __('app.supprimer') }}</button>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center">
            <p class="text-4xl mb-2">🏷️</p>
            <p class="text-gray-400">{{ __('app.aucune_marque') }}</p>
        </div>
        @endforelse
    </div>

    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-lg">{{ $marqueId ? __('app.modifier_marque') : __('app.nouvelle_marque') }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.nom_marque') }} *</label>
                    <input wire:model="nom" type="text"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('nom') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.pays_origine') }}</label>
                    <input wire:model="pays_origine" type="text"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-center gap-3">
                    <input wire:model="actif" type="checkbox" id="marque_actif" class="w-4 h-4 text-blue-600 rounded">
                    <label for="marque_actif" class="text-sm text-gray-700">{{ __('app.marque_active') }}</label>
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('showModal', false)"
                    class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">{{ __('app.annuler') }}</button>
                <button wire:click="sauvegarder"
                    class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">{{ __('app.sauvegarder') }}</button>
            </div>
        </div>
    </div>
    @endif

</div>
