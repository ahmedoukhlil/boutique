<div class="space-y-5 pb-20 lg:pb-0">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('app.gestion_stocks') }}</h1>
            <p class="text-sm text-gray-500">{{ __('app.inventaire') }}</p>
        </div>
    </div>

    {{-- Onglets --}}
    <div class="flex border-b border-gray-200 gap-4">
        @foreach(['stock' => __('app.onglet_stock'), 'mouvements' => __('app.onglet_mouvements'), 'alertes' => __('app.onglet_alertes')] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')"
            class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $activeTab === $tab ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            {{ $label }}
            @if($tab === 'alertes' && $this->alertes->count() > 0)
            <span class="ml-1 bg-red-100 text-red-600 text-xs px-1.5 py-0.5 rounded-full">{{ $this->alertes->count() }}</span>
            @endif
        </button>
        @endforeach
    </div>

    {{-- Tab : Stock --}}
    @if($activeTab === 'stock')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="{{ __('app.rechercher') }}..."
                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3 font-semibold text-gray-600">{{ __('app.produit') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 hidden sm:table-cell">{{ __('app.variante') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-center">{{ __('app.stock') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-center hidden md:table-cell">{{ __('app.alerte') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-right">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($this->stockItems as $variante)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-xl shrink-0">
                                    @if($variante->produit->categorie?->icone)
                                        {{ $variante->produit->categorie->icone }}
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $variante->produit->nom }}</p>
                                    <p class="text-xs text-gray-400">{{ $variante->produit->reference }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-gray-600">{{ $variante->libelle }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ $variante->quantite_stock === 0 ? 'bg-red-100 text-red-700' : ($variante->quantite_stock <= $variante->produit->stock_alerte ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                {{ $variante->quantite_stock }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center hidden md:table-cell text-gray-400 text-xs">{{ $variante->produit->stock_alerte }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="ouvrirEntreeStock({{ $variante->id }})"
                                    class="text-xs bg-green-50 text-green-700 font-medium px-2.5 py-1.5 rounded-lg hover:bg-green-100 transition-colors">
                                    {{ __('app.entree') }}
                                </button>
                                <button wire:click="ouvrirAjustement({{ $variante->id }})"
                                    class="text-xs bg-gray-100 text-gray-700 font-medium px-2.5 py-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                                    {{ __('app.ajuster') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">{{ __('app.aucun_article') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $this->stockItems->links() }}</div>
    </div>
    @endif

    {{-- Tab : Mouvements --}}
    @if($activeTab === 'mouvements')
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3 font-semibold text-gray-600">{{ __('app.date') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600">{{ __('app.produit') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-center">{{ __('app.type') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-center">{{ __('app.qte') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">{{ __('app.motif') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($this->mouvements as $mvt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $mvt->variante?->produit?->nom ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $mvt->variante?->libelle }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                {{ $mvt->type === 'ENTREE' ? 'bg-green-100 text-green-700' : ($mvt->type === 'SORTIE' ? 'bg-red-100 text-red-700' : ($mvt->type === 'RETOUR' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700')) }}">
                                {{ $mvt->type }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center font-semibold {{ in_array($mvt->type, ['ENTREE','RETOUR']) ? 'text-green-600' : 'text-red-600' }}">
                            {{ in_array($mvt->type, ['ENTREE','RETOUR']) ? '+' : '-' }}{{ $mvt->quantite }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs hidden md:table-cell">{{ $mvt->motif }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $this->mouvements->links() }}</div>
    </div>
    @endif

    {{-- Tab : Alertes --}}
    @if($activeTab === 'alertes')
    <div class="space-y-3">
        @forelse($this->alertes as $variante)
        <div class="bg-white rounded-xl border {{ $variante->quantite_stock === 0 ? 'border-red-200 bg-red-50' : 'border-amber-200 bg-amber-50' }} p-4 flex items-center gap-4">
            <span class="text-2xl">{{ $variante->quantite_stock === 0 ? '🔴' : '🟡' }}</span>
            <div class="flex-1">
                <p class="font-semibold text-gray-900">{{ $variante->produit->nom }}</p>
                <p class="text-sm text-gray-500">{{ $variante->libelle }}</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-xl {{ $variante->quantite_stock === 0 ? 'text-red-600' : 'text-amber-600' }}">{{ $variante->quantite_stock }}</p>
                <p class="text-xs text-gray-400">/ {{ $variante->produit->stock_alerte }} {{ __('app.seuil') }}</p>
            </div>
            <button wire:click="ouvrirEntreeStock({{ $variante->id }})"
                class="bg-green-600 text-white text-xs font-semibold px-3 py-2 rounded-lg hover:bg-green-700 shrink-0">
                {{ __('app.reapprovisionner') }}
            </button>
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <span class="text-4xl">✅</span>
            <p class="mt-3 text-gray-500 font-medium">{{ __('app.aucune_alerte_stock') }}</p>
            <p class="text-sm text-gray-400">{{ __('app.tous_produits_ok') }}</p>
        </div>
        @endforelse
    </div>
    @endif

    {{-- Modal entrée stock --}}
    @if($showEntreeModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-lg">{{ __('app.entree_stock_modal') }}</h3>
                <button wire:click="$set('showEntreeModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.quantite') }} *</label>
                        <input wire:model="quantiteEntree" type="number" min="1" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        @error('quantiteEntree') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.fournisseur') }}</label>
                        <select wire:model="fournisseurId" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            <option value="">{{ __('app.sans_fournisseur') }}</option>
                            @foreach($this->fournisseurs as $f)
                            <option value="{{ $f->id }}">{{ $f->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.num_commande') }}</label>
                        <input wire:model="numeroCde" type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.prix_achat') }}</label>
                        <input wire:model="prixAchat" type="number" min="0" step="0.01" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.date_reception') }} *</label>
                        <input wire:model="dateReception" type="date" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('showEntreeModal', false)" class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700">{{ __('app.annuler') }}</button>
                <button wire:click="enregistrerEntree" class="flex-1 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700">✅ {{ __('app.enregistrer') }}</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal ajustement --}}
    @if($showAjustementModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-sm rounded-t-2xl sm:rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-lg">{{ __('app.ajustement_stock') }}</h3>
                <button wire:click="$set('showAjustementModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.nouvelle_quantite') }} *</label>
                    <input wire:model="nouvelleQuantite" type="number" min="0" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.motif') }} *</label>
                    <select wire:model="motifAjust" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('app.select_categorie') }}</option>
                        <option value="Inventaire physique">{{ __('app.inventaire_physique') }}</option>
                        <option value="Casse / perte">{{ __('app.casse_perte') }}</option>
                        <option value="Vol">{{ __('app.vol') }}</option>
                        <option value="Correction erreur">{{ __('app.correction_erreur') }}</option>
                    </select>
                    @error('motifAjust') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('showAjustementModal', false)" class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700">{{ __('app.annuler') }}</button>
                <button wire:click="enregistrerAjustement" class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">{{ __('app.enregistrer') }}</button>
            </div>
        </div>
    </div>
    @endif

</div>
