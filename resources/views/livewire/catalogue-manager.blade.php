<div class="space-y-5 pb-20 lg:pb-0">

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('app.catalogue_produits') }}</h1>
        </div>
        <button wire:click="nouveauProduit"
            class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2.5 rounded-xl hover:bg-blue-700 transition-colors text-sm">
            {{ __('app.nouveau_produit_btn') }}
        </button>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 space-y-3">
        <div class="relative search-wrapper">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 search-icon">🔍</span>
            <input wire:model.live.debounce.300ms="search"
                   type="text" placeholder="{{ __('app.rechercher_produit') }}"
                   class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            <select wire:model.live="filtreCategorie"
                class="col-span-1 py-2 px-3 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-1 focus:ring-blue-400">
                <option value="">{{ __('app.toutes_categories') }}</option>
                @foreach($this->categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->icone }} {{ $cat->nom }}</option>
                @endforeach
            </select>
            <select wire:model.live="filtreMarque"
                class="py-2 px-3 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-1 focus:ring-blue-400">
                <option value="">{{ __('app.toutes_marques') }}</option>
                @foreach($this->marques as $marque)
                <option value="{{ $marque->id }}">{{ $marque->nom }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Grille produits --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($this->produits as $produit)
        @php $stockTotal = $produit->variantes->sum('quantite_stock'); @endphp
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                @if($produit->image)
                <img src="{{ Storage::disk('public')->url($produit->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                <div class="w-full h-full flex items-center justify-center">
                    @if($produit->categorie?->icone)
                    <span class="text-5xl">{{ $produit->categorie->icone }}</span>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    @endif
                </div>
                @endif
                @if($stockTotal === 0)
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ __('app.rupture') }}</span>
                </div>
                @elseif($stockTotal <= $produit->stock_alerte)
                <div class="absolute top-2 right-2">
                    <span class="bg-amber-400 text-white text-xs font-bold px-2 py-0.5 rounded-full">⚠️ {{ $stockTotal }}</span>
                </div>
                @endif
            </div>
            <div class="p-3">
                <p class="font-semibold text-gray-900 text-sm truncate">{{ $produit->nom }}</p>
                <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $produit->marque?->nom ?? $produit->categorie?->nom }}</p>
                <div class="flex items-center justify-between mt-2">
                    <span class="font-bold text-blue-600">{{ num($produit->prix_vente) }} {{ __('app.mru') }}</span>
                    <span class="text-xs text-gray-400">{{ $stockTotal }} {{ __('app.en_stock') }}</span>
                </div>
                <div class="flex gap-1.5 mt-3">
                    <button wire:click="editerProduit({{ $produit->id }})"
                        class="flex-1 py-1.5 text-xs bg-gray-100 hover:bg-gray-200 rounded-lg font-medium text-gray-700 transition-colors">
                        {{ __('app.editer_btn') }}
                    </button>
                    @if($produit->has_variantes)
                    <button wire:click="gererVariantes({{ $produit->id }})"
                        class="flex-1 py-1.5 text-xs bg-blue-50 hover:bg-blue-100 rounded-lg font-medium text-blue-700 transition-colors">
                        {{ __('app.variantes_btn') }}
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-gray-300 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p class="mt-3 text-gray-500">{{ __('app.aucun_produit') }}</p>
            <button wire:click="nouveauProduit" class="mt-3 text-blue-600 hover:underline text-sm">{{ __('app.ajouter_premier') }}</button>
        </div>
        @endforelse
    </div>

    {{ $this->produits->links() }}

    {{-- Modal produit --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-2xl rounded-t-2xl sm:rounded-2xl max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <h3 class="font-bold text-lg text-gray-900">{{ $produitId ? __('app.modifier_produit') : __('app.nouveau_produit') }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.nom_produit') }} *</label>
                        <input wire:model="nom" type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('nom') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.reference') }} *</label>
                        <input wire:model="reference" type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('reference') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.code_barre') }}</label>
                        <input wire:model="code_barre" type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.categorie') }} *</label>
                        <select wire:model="categorie_id" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('app.select_categorie') }}</option>
                            @foreach($this->categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->icone }} {{ $cat->nom }}</option>
                            @endforeach
                        </select>
                        @error('categorie_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.marque') }}</label>
                        <select wire:model="marque_id" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('app.sans_marque') }}</option>
                            @foreach($this->marques as $marque)
                            <option value="{{ $marque->id }}">{{ $marque->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.prix_vente_mru') }} *</label>
                        <input wire:model="prix_vente" type="number" min="0" step="0.01" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('prix_vente') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.prix_achat_mru') }}</label>
                        <input wire:model="prix_achat" type="number" min="0" step="0.01" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.seuil_alerte') }}</label>
                        <input wire:model="stock_alerte" type="number" min="0" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.photo_produit') }}</label>
                        <input wire:model="image" type="file" accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:border-0 file:rounded-lg file:bg-blue-50 file:text-blue-700 file:text-sm file:font-medium hover:file:bg-blue-100">
                    </div>
                    <div class="sm:col-span-2 flex items-center gap-3">
                        <input wire:model="has_variantes" type="checkbox" id="has_variantes" class="w-4 h-4 text-blue-600 rounded">
                        <label for="has_variantes" class="text-sm font-medium text-gray-700">{{ __('app.has_variantes') }}</label>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('app.description') }}</label>
                        <textarea wire:model="description" rows="2" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>
            </div>
            <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex gap-3">
                <button wire:click="$set('showModal', false)" class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">{{ __('app.annuler') }}</button>
                <button wire:click="sauvegarder" class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">{{ __('app.sauvegarder') }}</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal variantes --}}
    @if($showVarianteModal && $varianteProduitId)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-lg rounded-t-2xl sm:rounded-2xl max-h-[85vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <h3 class="font-bold text-lg text-gray-900">{{ __('app.gestion_variantes') }}</h3>
                <button wire:click="$set('showVarianteModal', false)" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                @foreach($variantes as $v)
                <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3">
                    @if($v['code_couleur'])
                    <div class="w-5 h-5 rounded-full border border-gray-300" style="background-color: {{ $v['code_couleur'] }}"></div>
                    @endif
                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ implode(' / ', array_filter([$v['taille'], $v['couleur']])) ?: 'Standard' }}</p>
                        <p class="text-xs text-gray-400">{{ __('app.stock') }}: {{ $v['quantite_stock'] }}</p>
                    </div>
                    <button wire:click="supprimerVariante({{ $v['id'] }})" wire:confirm="{{ __('app.supprimer_variante') }}"
                        class="text-red-400 hover:text-red-600 text-sm">🗑️</button>
                </div>
                @endforeach

                <div class="border-t pt-4">
                    <p class="text-sm font-semibold text-gray-700 mb-3">{{ __('app.ajouter_variante') }}</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ __('app.taille') }}</label>
                            <input wire:model="nvTaille" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ __('app.couleur') }}</label>
                            <input wire:model="nvCouleur" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ __('app.code_couleur') }}</label>
                            <input wire:model="nvCodeCouleur" type="color" class="w-full h-10 border border-gray-300 rounded-lg px-1">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ __('app.supplement_prix') }}</label>
                            <input wire:model="nvPrixSupplement" type="number" min="0" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">{{ __('app.stock_initial') }}</label>
                            <input wire:model="nvQuantiteStock" type="number" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-blue-400">
                        </div>
                    </div>
                    <button wire:click="ajouterVariante" class="mt-3 w-full py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">
                        {{ __('app.ajouter_cette_variante') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
