<div class="h-[calc(100vh-4rem)] lg:h-[calc(100vh-0rem)] -mx-4 -mt-6 lg:-mx-8 lg:-mt-6 flex flex-col">

    @if($showTicket && $factureCreeeId)
    {{-- Ticket de caisse --}}
    <div class="flex-1 flex items-center justify-center bg-gray-100 p-4">
        <div class="bg-white rounded-2xl shadow-lg w-full max-w-sm overflow-hidden">
            @php $facture = \App\Models\Facture::with('lignes','client')->find($factureCreeeId); @endphp
            <div class="bg-blue-600 px-6 py-5 text-center text-white">
                <p class="text-3xl mb-1">✅</p>
                <p class="font-bold text-lg">{{ __('app.vente_validee') }}</p>
                <p class="text-blue-200 text-sm">{{ $facture->numero }}</p>
            </div>
            <div class="px-6 py-4 space-y-3">
                @if($facture->client)
                <p class="text-sm text-gray-600">{{ __('app.clients') }} : <strong>{{ $facture->client->nom_complet }}</strong></p>
                @endif
                <div class="space-y-2">
                    @foreach($facture->lignes as $ligne)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-700">{{ $ligne->designation }} ×{{ $ligne->quantite }}</span>
                        <span class="font-medium">{{ num($ligne->total_ligne) }} MRU</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t pt-3 space-y-1">
                    <div class="flex justify-between font-bold text-base">
                        <span>{{ __('app.total_ttc') }}</span>
                        <span class="text-blue-600">{{ num($facture->total_ttc) }} {{ __('app.mru') }}</span>
                    </div>
                    @if($facture->monnaie_rendue > 0)
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>{{ __('app.monnaie_rendue') }}</span>
                        <span>{{ num($facture->monnaie_rendue) }} {{ __('app.mru') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="nouvelleVente"
                    class="flex-1 bg-blue-600 text-white font-semibold py-3 rounded-xl hover:bg-blue-700 transition-colors">
                    {{ __('app.nouvelle_vente') }}
                </button>
                <a href="{{ route('factures.show', $factureCreeeId) }}" target="_blank"
                    class="px-4 py-3 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition-colors text-sm font-medium"
                    title="Facture A5">
                    📄
                </a>
                <a href="{{ route('factures.ticket', $factureCreeeId) }}" target="_blank"
                    class="px-4 py-3 border border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 transition-colors text-sm font-medium"
                    title="Ticket caisse">
                    🖨️
                </a>
            </div>
        </div>
    </div>
    @else

    {{-- Interface POS --}}
    <div class="flex flex-col lg:flex-row flex-1 overflow-hidden">

        {{-- Colonne gauche : recherche + résultats --}}
        <div class="flex-1 flex flex-col bg-gray-50 overflow-hidden">

            {{-- Barre de recherche --}}
            <div class="bg-white border-b border-gray-200 px-4 py-3">
                <div class="flex items-center gap-2">
                    <div class="flex-1 relative search-wrapper">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 search-icon">🔍</span>
                        <input wire:model.live.debounce.300ms="searchArticle"
                               wire:keydown.enter="$refresh"
                               type="text"
                               placeholder="{{ __('app.rechercher_article') }}"
                               autofocus
                               class="w-full pl-9 pr-4 py-2.5 bg-gray-100 rounded-xl text-sm border-0 focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    </div>
                    @if($searchArticle)
                    <button wire:click="$set('searchArticle', '')" class="text-gray-400 hover:text-gray-600">✕</button>
                    @endif
                </div>
            </div>

            {{-- Grille articles --}}
            <div class="flex-1 overflow-y-auto">
                @if($this->resultatsRecherche->isNotEmpty())
                <div class="p-3">
                    @if(!$searchArticle)
                    <p class="text-xs text-gray-400 font-medium px-1 mb-2">⭐ {{ __('app.les_plus_vendus') }}</p>
                    @endif
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-3">
                        @foreach($this->resultatsRecherche as $variante)
                        <button wire:click="ajouterAuPanier({{ $variante->id }})"
                            class="bg-white rounded-xl border border-gray-200 p-3 text-left hover:border-blue-300 hover:shadow-md transition-all active:scale-95">
                            <div class="aspect-square rounded-lg bg-gray-100 mb-2 overflow-hidden flex items-center justify-center relative">
                                @if($variante->produit->image)
                                <img src="{{ Storage::disk('public')->url($variante->produit->image) }}" class="w-full h-full object-cover">
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                @endif
                                @if($variante->total_vendu > 0 && !$searchArticle)
                                <span class="absolute top-1 right-1 bg-blue-600 text-white text-xs font-bold px-1.5 py-0.5 rounded-full leading-none">
                                    {{ $variante->total_vendu }}×
                                </span>
                                @endif
                            </div>
                            <p class="text-xs font-semibold text-gray-900 truncate">{{ $variante->produit->nom }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $variante->libelle }}</p>
                            @if($variante->produit->categorie || $variante->produit->marque)
                            <p class="text-xs text-gray-400 truncate mt-0.5">
                                {{ implode(' · ', array_filter([$variante->produit->categorie?->nom, $variante->produit->marque?->nom])) }}
                            </p>
                            @endif
                            <div class="flex items-center justify-between mt-1.5">
                                <span class="text-sm font-bold text-blue-600">{{ num($variante->prix_final) }} MRU</span>
                                <span class="text-xs {{ $variante->quantite_stock <= 3 ? 'text-amber-500 font-medium' : 'text-gray-400' }}">
                                    {{ $variante->quantite_stock <= 3 ? '⚠️ ' : '' }}{{ $variante->quantite_stock }}
                                </span>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @elseif(strlen($searchArticle) >= 2)
                <div class="flex flex-col items-center justify-center h-48 text-gray-400">
                    <span class="text-4xl mb-2">🔍</span>
                    <p class="text-sm">Aucun article trouvé pour "{{ $searchArticle }}"</p>
                </div>
                @else
                <div class="flex flex-col items-center justify-center h-full text-gray-300 p-8">
                    <span class="text-5xl mb-3">📦</span>
                    <p class="text-sm text-gray-400">Aucun article en stock</p>
                </div>
                @endif

                @error('stock')
                <div class="mx-3 mt-2 bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-lg text-sm">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Colonne droite : panier + paiement --}}
        <div class="w-full lg:w-96 bg-white border-t lg:border-t-0 lg:border-l border-gray-200 flex flex-col max-h-[50vh] lg:max-h-none">

            {{-- Client --}}
            <div class="px-4 pt-3 pb-2 border-b border-gray-100">
                @if($this->clientSelectionne)
                <div class="flex items-center justify-between bg-blue-50 rounded-xl px-3 py-2">
                    <div class="flex items-center gap-2">
                        <span class="text-base">👤</span>
                        <div>
                            <p class="text-xs font-semibold text-blue-800">{{ $this->clientSelectionne->nom_complet }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-blue-600">{{ $this->clientSelectionne->points_fidelite }} pts</span>
                                @if($this->clientSelectionne->solde < 0)
                                <span class="text-xs font-bold text-red-600">{{ __('app.dette') }} : {{ num(abs($this->clientSelectionne->solde)) }} {{ __('app.mru') }}</span>
                                @elseif($this->clientSelectionne->solde > 0)
                                <span class="text-xs font-bold text-green-600">{{ __('app.credit') }} : {{ num($this->clientSelectionne->solde) }} {{ __('app.mru') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button wire:click="$set('clientId', null)" class="text-blue-400 hover:text-blue-600 text-xs">✕</button>
                </div>
                @else
                <div class="relative">
                    <input wire:model.live.debounce.300ms="clientSearch"
                           wire:focus="$set('showClientSearch', true)"
                           type="text" placeholder="{{ __('app.client_optionnel') }}"
                           class="w-full pl-8 pr-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-blue-400 focus:border-blue-400">
                    <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">👤</span>
                    @if($this->clients->isNotEmpty())
                    <div class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-xl shadow-lg z-10 mt-1 overflow-hidden">
                        @foreach($this->clients as $client)
                        <button wire:click="$set('clientId', {{ $client->id }}); $set('clientSearch', '')"
                            class="w-full text-left px-3 py-2 text-xs hover:bg-blue-50 border-b border-gray-50 last:border-0">
                            <span class="font-medium">{{ $client->nom_complet }}</span>
                            <span class="text-gray-400 ml-1">{{ $client->telephone }}</span>
                        </button>
                        @endforeach
                    </div>
                    @endif
                </div>
                @endif
            </div>

            {{-- Panier --}}
            <div class="flex-1 overflow-y-auto divide-y divide-gray-50">
                @forelse($panier as $key => $item)
                <div class="flex items-center gap-3 px-4 py-2.5">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item['nom'] }}</p>
                        <p class="text-xs text-gray-400 truncate">
                            {{ implode(' · ', array_filter([$item['categorie'] ?? null, $item['marque'] ?? null, $item['libelle_variante'] !== 'Standard' ? $item['libelle_variante'] : null])) }}
                        </p>
                        <p class="text-xs text-gray-400">{{ num($item['prix']) }} MRU</p>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button wire:click="modifierQuantite('{{ $key }}', -1)"
                            class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200 font-bold text-sm">−</button>
                        <span class="w-6 text-center text-sm font-semibold">{{ $item['quantite'] }}</span>
                        <button wire:click="modifierQuantite('{{ $key }}', 1)"
                            class="w-7 h-7 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600 hover:bg-gray-200 font-bold text-sm">+</button>
                    </div>
                    <p class="text-sm font-bold text-gray-900 w-20 text-right">{{ num($item['total']) }}</p>
                    <button wire:click="retirerDuPanier('{{ $key }}')" class="text-red-300 hover:text-red-500 text-sm ml-1">✕</button>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-12 text-gray-300">
                    <span class="text-4xl mb-2">🛒</span>
                    <p class="text-sm text-gray-400">{{ __('app.panier_vide') }}</p>
                </div>
                @endforelse
            </div>

            {{-- Totaux & paiement --}}
            <div class="border-t border-gray-200 px-4 py-3 space-y-3">

                {{-- Remise --}}
                @if(!empty($panier))
                <div class="flex items-center gap-2">
                    <label class="text-xs text-gray-500 shrink-0">{{ __('app.remise') }}</label>
                    <input wire:model.live="remisePourcent" wire:change="recalculer"
                           type="number" min="0" max="100" step="1"
                           class="flex-1 py-1.5 px-3 text-sm border border-gray-200 rounded-lg text-center focus:ring-1 focus:ring-blue-400">
                </div>
                @endif

                {{-- Total --}}
                <div class="bg-gray-50 rounded-xl px-4 py-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-700">{{ __('app.total_a_payer') }}</span>
                        <span class="text-xl font-bold text-blue-600">{{ num($totalPanier) }} {{ __('app.mru') }}</span>
                    </div>
                </div>

                {{-- Mode de paiement --}}
                @if($this->modesPaiement->isNotEmpty())
                <div class="grid gap-1.5" style="grid-template-columns: repeat({{ min($this->modesPaiement->count(), 3) }}, minmax(0, 1fr))">
                    @foreach($this->modesPaiement as $mp)
                    <button wire:click="$set('modePaiement', '{{ $mp->code }}')"
                        class="py-2 px-2 text-xs font-medium rounded-lg border transition-colors {{ $modePaiement === $mp->code ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                        {{ $mp->icone }} {{ $mp->nom }}
                    </button>
                    @endforeach
                </div>
                @endif

                {{-- Montant reçu --}}
                <div class="space-y-1.5">
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-gray-500 shrink-0">{{ __('app.recu') }}</label>
                        <input
                               x-data="{ val: {{ $montantRecu }} }"
                               x-init="$watch('val', v => $wire.set('montantRecu', v))"
                               x-effect="val = {{ $montantRecu }}"
                               x-model="val"
                               type="number" min="0" step="500"
                               class="flex-1 py-2 px-3 text-sm border border-gray-200 rounded-lg focus:ring-1 focus:ring-blue-400">
                        @if($monnaieRendue > 0)
                        <div class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-lg shrink-0">
                            {{ __('app.monnaie_rendue') }}: {{ num($monnaieRendue) }}
                        </div>
                        @endif
                    </div>
                    {{-- Indicateur paiement partiel --}}
                    @if($montantRecu > 0 && $montantRecu < $totalPanier && $totalPanier > 0)
                        @if($clientId)
                        <div class="bg-amber-50 border border-amber-200 rounded-lg px-3 py-1.5 flex items-center justify-between">
                            <span class="text-xs text-amber-700 font-medium">{{ __('app.valider_partiel') }}</span>
                            <span class="text-xs font-bold text-red-600">{{ __('app.reste_a_regler') }} : {{ num($totalPanier - $montantRecu) }} {{ __('app.mru') }}</span>
                        </div>
                        @else
                        <div class="bg-red-50 border border-red-200 rounded-lg px-3 py-1.5">
                            <span class="text-xs text-red-600">{{ __('app.paiement_partiel_client') }}</span>
                        </div>
                        @endif
                    @endif
                </div>

                @error('paiement')
                <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                {{-- Bouton valider --}}
                @php
                    $peutValider = !empty($panier) && $montantRecu > 0 && (
                        $montantRecu >= $totalPanier ||
                        ($montantRecu < $totalPanier && $clientId)
                    );
                @endphp
                <button wire:click="validerVente"
                    @if(!$peutValider) disabled @endif
                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed text-white font-bold py-3.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <span>✅</span>
                    <span>{{ ($montantRecu > 0 && $montantRecu < $totalPanier && $clientId) ? __('app.valider_partiel') : __('app.valider_vente') }}</span>
                </button>

                @if(!empty($panier))
                <button wire:click="viderPanier" wire:confirm="{{ __('app.vider_panier') }} ?"
                    class="w-full text-xs text-gray-400 hover:text-red-500 transition-colors py-1">
                    {{ __('app.vider_panier') }}
                </button>
                @endif

            </div>
        </div>
    </div>
    @endif

    {{-- Modale création rapide client --}}
    @if($showModalNouveauClient)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-sm rounded-t-2xl sm:rounded-2xl shadow-xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900">{{ __('app.nouveau_client') }}</h3>
                    <p class="text-xs text-gray-400">{{ __('app.aucun_client') }}</p>
                </div>
                <button wire:click="annulerNouveauClient" class="text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.telephone') }} *</label>
                    <input wire:model="newClientTelephone" type="tel" autofocus
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('newClientTelephone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.nom') }}</label>
                        <input wire:model="newClientNom" type="text" placeholder="{{ __('app.nom') }}..."
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.prenom') }}</label>
                        <input wire:model="newClientPrenom" type="text" placeholder="{{ __('app.prenom') }}..."
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="annulerNouveauClient"
                    class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ __('app.annuler') }}
                </button>
                <button wire:click="creerClientRapide"
                    class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">
                    {{ __('app.nouveau_client') }}
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
