<div class="space-y-5 pb-20 lg:pb-0">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('app.categories') }}</h1>
        </div>
        <button wire:click="nouvelle"
            class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2.5 rounded-xl hover:bg-blue-700 text-sm">
            {{ __('app.nouvelle_categorie') }}
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($this->categories as $cat)
        <div class="bg-white rounded-xl border border-gray-200 p-4 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0"
                 style="background-color: {{ $cat->couleur }}20; border: 2px solid {{ $cat->couleur }}40;">
                {{ $cat->icone ?? '🏷️' }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900">{{ $cat->nom }}</p>
                @if($cat->parent)
                <p class="text-xs text-gray-400">{{ $cat->parent->nom }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-0.5">{{ $cat->produits_count }} {{ __('app.nb_produits') }}</p>
            </div>
            <div class="flex flex-col gap-1 shrink-0">
                <button wire:click="editer({{ $cat->id }})"
                    class="text-xs text-blue-600 hover:text-blue-800 px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100">
                    ✏️
                </button>
                <button wire:click="toggleActif({{ $cat->id }})"
                    class="text-xs px-2 py-1 rounded-lg {{ $cat->actif ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-gray-100 text-gray-400 hover:bg-gray-200' }}">
                    {{ $cat->actif ? '✅' : '⏸️' }}
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center">
            <span class="text-4xl">🏷️</span>
            <p class="mt-3 text-gray-400">{{ __('app.aucune_categorie') }}</p>
        </div>
        @endforelse
    </div>

    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-sm rounded-t-2xl sm:rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-lg">{{ $categorieId ? __('app.modifier') : __('app.nouvelle_categorie') }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.nom') }} *</label>
                    <input wire:model="nom" type="text"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('nom') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.icone') }}</label>
                        <input wire:model="icone" type="text" placeholder="📦" maxlength="4"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm text-center text-2xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.couleur') }}</label>
                        <input wire:model="couleur" type="color"
                            class="w-full h-11 border border-gray-300 rounded-xl px-1 cursor-pointer">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.categorie_parente') }}</label>
                    <select wire:model="parent_id" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('app.aucune_parente') }}</option>
                        @foreach($this->categoriesParentes as $p)
                        <option value="{{ $p->id }}">{{ $p->icone }} {{ $p->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('showModal', false)" class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700">{{ __('app.annuler') }}</button>
                <button wire:click="sauvegarder" class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">{{ __('app.sauvegarder') }}</button>
            </div>
        </div>
    </div>
    @endif

</div>
