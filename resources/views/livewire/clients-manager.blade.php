<div class="space-y-5 pb-20 lg:pb-0">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('app.clients') }}</h1>
            <p class="text-sm text-gray-500">{{ __('app.rechercher') }}</p>
        </div>
        <button wire:click="nouveauClient"
            class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2.5 rounded-xl hover:bg-blue-700 text-sm">
            ➕ {{ __('app.nouveau_client') }}
        </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="relative search-wrapper">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 search-icon">🔍</span>
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="{{ __('app.rechercher') }}..."
                class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3 font-semibold text-gray-600">{{ __('app.clients') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 hidden sm:table-cell">{{ __('app.telephone') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">{{ __('app.email') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-right hidden sm:table-cell">{{ __('app.solde') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-right">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($this->clients as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                    <span class="text-blue-700 text-sm font-semibold">{{ strtoupper(substr($client->nom, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $client->nom_complet }}</p>
                                    @if($client->adresse)
                                    <p class="text-xs text-gray-400 truncate max-w-32">{{ $client->adresse }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">{{ $client->telephone ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs hidden md:table-cell">{{ $client->email ?? '—' }}</td>
                        <td class="px-4 py-3 text-right hidden sm:table-cell">
                            @if($client->solde < 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                                    {{ __('app.dette') }} {{ num(abs($client->solde)) }} {{ __('app.mru') }}
                                </span>
                            @elseif($client->solde > 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    +{{ num($client->solde) }} {{ __('app.mru') }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="voirDetail({{ $client->id }})"
                                    class="text-xs text-gray-500 hover:text-gray-700 px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200">
                                    👁️ {{ __('app.detail') }}
                                </button>
                                <button wire:click="editerClient({{ $client->id }})"
                                    class="text-xs text-blue-600 hover:text-blue-800 px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100">
                                    ✏️ {{ __('app.editer') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">{{ __('app.aucun_client') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $this->clients->links() }}</div>
    </div>

    {{-- Modal formulaire --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-lg">{{ $clientId ? __('app.modifier_client') : __('app.nouveau_client') }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.nom') }} *</label>
                        <input wire:model="nom" type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        @error('nom') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.prenom') }}</label>
                        <input wire:model="prenom" type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.telephone') }}</label>
                        <input wire:model="telephone" type="tel" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.email') }}</label>
                        <input wire:model="email" type="email" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.adresse') }}</label>
                        <input wire:model="adresse" type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('showModal', false)" class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700">{{ __('app.annuler') }}</button>
                <button wire:click="sauvegarder" class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">{{ __('app.sauvegarder') }}</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal détail client --}}
    @if($showDetail && $this->clientDetail)
    @php $cd = $this->clientDetail; @endphp
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-lg rounded-t-2xl sm:rounded-2xl max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="font-bold text-lg">{{ $cd->nom_complet }}</h3>
                <button wire:click="$set('showDetail', false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="px-6 py-5 space-y-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                        <span class="text-blue-700 text-xl font-bold">{{ strtoupper(substr($cd->nom, 0, 1)) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        @if($cd->telephone) <p class="text-sm text-gray-600">📞 {{ $cd->telephone }}</p> @endif
                        @if($cd->email) <p class="text-sm text-gray-600">✉️ {{ $cd->email }}</p> @endif
                        @if($cd->adresse) <p class="text-sm text-gray-500 truncate">📍 {{ $cd->adresse }}</p> @endif
                    </div>
                </div>

                {{-- Solde --}}
                <div class="rounded-xl p-4 {{ $cd->solde < 0 ? 'bg-red-50 border border-red-200' : ($cd->solde > 0 ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200') }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase mb-1 {{ $cd->solde < 0 ? 'text-red-500' : ($cd->solde > 0 ? 'text-green-600' : 'text-gray-500') }}">
                                {{ $cd->solde < 0 ? __('app.dette_client') : ($cd->solde > 0 ? __('app.credit_client') : __('app.solde_equilibre')) }}
                            </p>
                            <p class="text-2xl font-bold {{ $cd->solde < 0 ? 'text-red-700' : ($cd->solde > 0 ? 'text-green-700' : 'text-gray-700') }}">
                                {{ $cd->solde < 0 ? '-' : ($cd->solde > 0 ? '+' : '') }}{{ num(abs($cd->solde)) }} {{ __('app.mru') }}
                            </p>
                        </div>
                        @if($cd->solde < 0)
                        <button wire:click="ouvrirReglement"
                            class="bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-red-700">
                            💳 {{ __('app.regler') }}
                        </button>
                        @endif
                    </div>
                </div>

                <div class="bg-blue-50 rounded-xl p-4">
                    <p class="text-xs font-semibold text-blue-500 uppercase mb-1">{{ __('app.total_achats') }}</p>
                    <p class="text-xl font-bold text-blue-700">{{ num($cd->total_achats) }} {{ __('app.mru') }}</p>
                </div>

                @if($cd->factures->isNotEmpty())
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-2">{{ __('app.dernieres_factures') }}</p>
                    <div class="space-y-2">
                        @foreach($cd->factures as $f)
                        <div class="flex justify-between items-center bg-gray-50 rounded-lg px-3 py-2">
                            <div>
                                <p class="text-xs font-medium text-gray-800">{{ $f->numero }}</p>
                                <p class="text-xs text-gray-400">{{ $f->created_at->format('d/m/Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-blue-600">{{ num($f->total_ttc) }} {{ __('app.mru') }}</p>
                                @if($f->statut === 'partielle')
                                <span class="text-xs text-red-500 font-medium">{{ __('app.partielle') }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($cd->reglements->isNotEmpty())
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-2">{{ __('app.historique_reglements') }}</p>
                    <div class="space-y-2">
                        @foreach($cd->reglements as $r)
                        <div class="flex justify-between items-start bg-gray-50 rounded-lg px-3 py-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500">{{ $r->created_at->format('d/m/Y H:i') }}</p>
                                @if($r->note) <p class="text-xs text-gray-600 truncate">{{ $r->note }}</p> @endif
                            </div>
                            <span class="text-sm font-bold ml-3 {{ $r->montant >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $r->montant >= 0 ? '+' : '' }}{{ num($r->montant) }} {{ __('app.mru') }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Modal règlement dette --}}
    @if($showReglementModal)
    <div class="fixed inset-0 bg-black/60 z-[60] flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-sm rounded-t-2xl sm:rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-lg">{{ __('app.enregistrer_reglement') }}</h3>
                <button wire:click="$set('showReglementModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.montant') }} *</label>
                    <div class="relative search-wrapper">
                        <input wire:model.live="montantReglement" type="number" min="1" step="0.01"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm pr-14 focus:ring-2 focus:ring-blue-500">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-400 font-medium">{{ __('app.mru') }}</span>
                    </div>
                    @error('montantReglement') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.mode_paiement') }} *</label>
                    <select wire:model="modeReglementCode" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        @foreach($this->modesPaiement as $mode)
                        <option value="{{ $mode->code }}">{{ $mode->nom }}</option>
                        @endforeach
                    </select>
                    @error('modeReglementCode') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.note') }}</label>
                    <input wire:model="noteReglement" type="text"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('showReglementModal', false)"
                    class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700">{{ __('app.annuler') }}</button>
                <button wire:click="enregistrerReglement"
                    class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">
                    {{ __('app.enregistrer') }}
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
