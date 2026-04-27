<div class="space-y-6 pb-4">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('app.tableau_de_bord_titre') }}</h1>
            <p class="text-sm text-gray-500">{{ now()->isoFormat('dddd D MMMM YYYY') }}</p>
        </div>
        <div class="flex rounded-lg border border-gray-200 overflow-hidden text-xs">
            @foreach(['today' => __('app.periode_auj'), 'week' => __('app.periode_semaine'), 'month' => __('app.periode_mois')] as $val => $label)
            <button wire:click="$set('periode', '{{ $val }}')"
                class="px-3 py-1.5 font-medium transition-colors {{ $periode === $val ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-xs">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium">{{ __('app.chiffre_affaires') }}</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ num($this->stats['chiffre_affaires']) }} {{ __('app.mru') }}</p>
                </div>
                <span class="text-2xl">💰</span>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-xs">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium">{{ __('app.ventes') }}</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $this->stats['nb_ventes'] }}</p>
                </div>
                <span class="text-2xl">🛒</span>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-xs">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-medium">{{ __('app.clients') }}</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $this->stats['nb_clients'] }}</p>
                </div>
                <span class="text-2xl">👥</span>
            </div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-xs {{ $this->stats['ruptures'] > 0 ? 'border-red-200 bg-red-50' : '' }}">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs {{ $this->stats['ruptures'] > 0 ? 'text-red-500' : 'text-gray-500' }} font-medium">{{ __('app.ruptures') }}</p>
                    <p class="text-xl font-bold {{ $this->stats['ruptures'] > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $this->stats['ruptures'] }}</p>
                </div>
                <span class="text-2xl">⚠️</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top produits --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900 text-sm">{{ __('app.top_produits') }}</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($this->topProduits as $i => $item)
                <div class="flex items-center gap-3 px-4 py-3">
                    <span class="text-sm font-bold text-gray-400 w-5">{{ $i+1 }}</span>
                    @if($item->variante?->produit?->image)
                    <img src="{{ Storage::disk('public')->url($item->variante->produit->image) }}"
                         class="w-10 h-10 rounded-lg object-cover bg-gray-100">
                    @else
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item->variante?->produit?->nom ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $item->total_vendu }} {{ __('app.vendus') }}</p>
                    </div>
                    <p class="text-sm font-semibold text-blue-600">{{ num($item->total_ca) }} {{ __('app.mru') }}</p>
                </div>
                @empty
                <p class="px-4 py-8 text-center text-sm text-gray-400">{{ __('app.aucune_vente') }}</p>
                @endforelse
            </div>
        </div>

        {{-- Alertes stock --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-xs overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900 text-sm">{{ __('app.alertes_stock') }}</h2>
                @if($this->alertesStock->count() > 0)
                <span class="bg-red-100 text-red-600 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $this->alertesStock->count() }}</span>
                @endif
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($this->alertesStock as $variante)
                <div class="flex items-center gap-3 px-4 py-3">
                    <div class="w-2 h-2 rounded-full {{ $variante->quantite_stock === 0 ? 'bg-red-500' : 'bg-amber-400' }} shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $variante->produit->nom }}</p>
                        <p class="text-xs text-gray-400">{{ $variante->libelle }}</p>
                    </div>
                    <span class="text-sm font-bold {{ $variante->quantite_stock === 0 ? 'text-red-600' : 'text-amber-600' }}">
                        {{ $variante->quantite_stock }}
                    </span>
                </div>
                @empty
                <p class="px-4 py-8 text-center text-sm text-gray-400">{{ __('app.aucune_alerte') }}</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Accès rapides --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <a href="{{ route('pos') }}" class="bg-blue-600 text-white rounded-xl p-4 flex items-center gap-3 hover:bg-blue-700 transition-colors">
            <span class="text-2xl">🛒</span>
            <div>
                <p class="font-semibold text-sm">{{ __('app.ouvrir_caisse') }}</p>
                <p class="text-xs text-blue-200">{{ __('app.nouvelle_vente_desc') }}</p>
            </div>
        </a>
        <a href="{{ route('catalogue') }}" class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 hover:bg-gray-50 transition-colors">
            <span class="text-2xl">➕</span>
            <div>
                <p class="font-semibold text-sm text-gray-900">{{ __('app.nouveau_produit') }}</p>
                <p class="text-xs text-gray-400">{{ __('app.nouveau_produit_desc') }}</p>
            </div>
        </a>
        <a href="{{ route('stock') }}" class="bg-white border border-gray-200 rounded-xl p-4 flex items-center gap-3 hover:bg-gray-50 transition-colors">
            <span class="text-2xl">📦</span>
            <div>
                <p class="font-semibold text-sm text-gray-900">{{ __('app.entree_stock') }}</p>
                <p class="text-xs text-gray-400">{{ __('app.reapprovisionner_desc') }}</p>
            </div>
        </a>
    </div>

</div>
