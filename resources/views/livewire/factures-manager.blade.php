<div class="space-y-5 pb-20 lg:pb-0">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('app.factures') }}</h1>
            <p class="text-sm text-gray-500">{{ __('app.historique_ventes') }}</p>
        </div>
        <a href="{{ route('pos') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2.5 rounded-xl hover:bg-blue-700 text-sm">
            🛒 {{ __('app.nouvelle_vente_desc') }}
        </a>
    </div>

    {{-- Totaux --}}
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500 font-medium">{{ __('app.factures_periode') }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $this->totaux['count'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500 font-medium">{{ __('app.chiffre_affaires') }}</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ num($this->totaux['total']) }} {{ __('app.mru') }}</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-xl border border-gray-200 p-4 space-y-3">
        <div class="relative search-wrapper">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 search-icon">🔍</span>
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="{{ __('app.num_client') }}"
                class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="grid grid-cols-3 gap-2">
            <select wire:model.live="filtreStatut" class="py-2 px-3 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-1 focus:ring-blue-400">
                <option value="">{{ __('app.tous_statuts') }}</option>
                <option value="payee">{{ __('app.payee') }}</option>
                <option value="en_cours">{{ __('app.partielle') }}</option>
                <option value="annulee">{{ __('app.annulee') }}</option>
            </select>
            <input wire:model.live="dateDebut" type="date" class="py-2 px-3 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-1 focus:ring-blue-400">
            <input wire:model.live="dateFin" type="date" class="py-2 px-3 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-1 focus:ring-blue-400">
        </div>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3 font-semibold text-gray-600">{{ __('app.numero_facture') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 hidden sm:table-cell">{{ __('app.clients') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">{{ __('app.date') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-center">{{ __('app.statut') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 hidden sm:table-cell text-center">{{ __('app.paiement') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-right">{{ __('app.total') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-right">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($this->factures as $facture)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-700">{{ $facture->numero }}</td>
                        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">
                            {{ $facture->client?->nom_complet ?? __('app.client_anonyme') }}
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs hidden md:table-cell">
                            {{ $facture->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $sc = match($facture->statut) {
                                    'payee' => 'bg-green-100 text-green-700',
                                    'en_cours', 'partielle' => 'bg-yellow-100 text-yellow-700',
                                    'annulee' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                                $label = match($facture->statut) {
                                    'payee' => __('app.payee'),
                                    'en_cours', 'partielle' => __('app.partielle'),
                                    'annulee' => __('app.annulee'),
                                    default => $facture->statut,
                                };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $sc }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500 hidden sm:table-cell capitalize">
                            {{ $facture->mode_paiement }}
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-900">
                            {{ num($facture->total_ttc) }} {{ __('app.mru') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('factures.show', $facture->id) }}" target="_blank"
                                    class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200" title="{{ __('app.imprimer') }}">
                                    📄
                                </a>
                                <a href="{{ route('factures.ticket', $facture->id) }}" target="_blank"
                                    class="text-xs text-gray-500 hover:text-gray-700 px-2 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200" title="Ticket">
                                    🖨️
                                </a>
                                @if($facture->statut === 'payee')
                                <button wire:click="annulerFacture({{ $facture->id }})" wire:confirm="{{ __('app.annuler_facture') }}"
                                    class="text-xs text-red-500 hover:text-red-700 px-2 py-1.5 rounded-lg bg-red-50 hover:bg-red-100">
                                    ✕
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">{{ __('app.aucune_facture') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $this->factures->links() }}</div>
    </div>

</div>
