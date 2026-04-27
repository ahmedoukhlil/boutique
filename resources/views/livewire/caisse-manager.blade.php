<div class="space-y-5 pb-20 lg:pb-0">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('app.journal_caisse') }}</h1>
            <p class="text-sm text-gray-500">{{ __('app.suivi_journalier') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <input wire:model.live="dateFiltre" type="date"
                class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            <button wire:click="$set('showModal', true)"
                class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2 rounded-xl hover:bg-blue-700 text-sm">
                {{ __('app.operation') }}
            </button>
        </div>
    </div>

    {{-- Statistiques du jour --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500 font-medium">{{ __('app.ventes_label') }}</p>
            <p class="text-xl font-bold text-green-600 mt-1">{{ num($this->totalVentes) }} {{ __('app.mru') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $this->nbVentes }} {{ __('app.transactions') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500 font-medium">{{ __('app.remboursements') }}</p>
            <p class="text-xl font-bold text-red-500 mt-1">{{ num($this->totalRemboursements) }} {{ __('app.mru') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <p class="text-xs text-gray-500 font-medium">{{ __('app.autres_entrees') }}</p>
            <p class="text-xl font-bold text-blue-600 mt-1">{{ num($this->totalEntrees) }} {{ __('app.mru') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 {{ $this->soldeJour >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
            <p class="text-xs text-gray-500 font-medium">{{ __('app.solde_jour') }}</p>
            <p class="text-xl font-bold {{ $this->soldeJour >= 0 ? 'text-green-700' : 'text-red-700' }} mt-1">
                {{ num($this->soldeJour) }} {{ __('app.mru') }}
            </p>
        </div>
    </div>

    {{-- Liste des opérations --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900 text-sm">{{ __('app.operations_du') }} {{ \Carbon\Carbon::parse($dateFiltre)->isoFormat('dddd D MMMM') }}</h2>
        </div>
        @if($this->operations->isEmpty())
        <div class="py-16 text-center">
            <span class="text-4xl">💰</span>
            <p class="mt-3 text-gray-400">{{ __('app.aucune_operation') }}</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3 font-semibold text-gray-600">{{ __('app.heure') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600">{{ __('app.type') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 hidden sm:table-cell">{{ __('app.reference') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 hidden md:table-cell">{{ __('app.mode') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-right">{{ __('app.montant') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($this->operations as $op)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $op->created_at->format('H:i') }}</td>
                        <td class="px-4 py-3">
                            @php
                                $config = match($op->type) {
                                    'VENTE'         => ['bg-green-100 text-green-700', '🛒 ' . __('app.ventes_label')],
                                    'REMBOURSEMENT' => ['bg-red-100 text-red-700', '↩️'],
                                    'ENTREE_CAISSE' => ['bg-blue-100 text-blue-700', '⬇️'],
                                    'SORTIE_CAISSE' => ['bg-orange-100 text-orange-700', '⬆️'],
                                    default         => ['bg-gray-100 text-gray-700', $op->type],
                                };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $config[0] }}">
                                {{ $config[1] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 hidden sm:table-cell">
                            {{ $op->facture?->numero ?? $op->notes ?? '—' }}
                            @if($op->facture?->client)
                            <span class="text-gray-400 text-xs ml-1">· {{ $op->facture->client->nom }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs hidden md:table-cell capitalize">{{ $op->mode_paiement }}</td>
                        <td class="px-4 py-3 text-right font-bold {{ in_array($op->type, ['VENTE','ENTREE_CAISSE']) ? 'text-green-600' : 'text-red-600' }}">
                            {{ in_array($op->type, ['VENTE','ENTREE_CAISSE']) ? '+' : '-' }}{{ num($op->montant) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Modal opération manuelle --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-sm rounded-t-2xl sm:rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-lg">{{ __('app.operation_manuelle') }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.type') }} *</label>
                    <select wire:model="typeOperation" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="ENTREE_CAISSE">{{ __('app.entree_caisse') }}</option>
                        <option value="SORTIE_CAISSE">{{ __('app.sortie_caisse') }}</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.montant_mru') }} *</label>
                    <input wire:model="montant" type="number" min="0.01" step="0.01"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('montant') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.mode_paiement') }}</label>
                    <select wire:model="modePaiement" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        @foreach($this->modesPaiement as $mp)
                        <option value="{{ $mp->nom }}">{{ $mp->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.notes') }}</label>
                    <input wire:model="notes" type="text" placeholder="{{ __('app.motif_operation') }}"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('showModal', false)" class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700">{{ __('app.annuler') }}</button>
                <button wire:click="enregistrerOperation" class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">{{ __('app.enregistrer') }}</button>
            </div>
        </div>
    </div>
    @endif

</div>
