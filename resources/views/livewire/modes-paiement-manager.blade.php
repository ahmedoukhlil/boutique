<div class="space-y-5 pb-20 lg:pb-0">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('app.modes_paiement') }}</h1>
            <p class="text-sm text-gray-500">{{ __('app.modes_pos') }}</p>
        </div>
        <button wire:click="nouveau"
            class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2.5 rounded-xl hover:bg-blue-700 text-sm">
            {{ __('app.nouveau_mode') }}
        </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
        @forelse($this->modes as $mode)
        <div class="flex items-center gap-4 px-5 py-4">
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 text-sm">{{ $mode->nom }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button wire:click="toggleActif({{ $mode->id }})"
                    class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $mode->actif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $mode->actif ? __('app.actif_label') : __('app.inactif_label') }}
                </button>
                <button wire:click="editer({{ $mode->id }})"
                    class="text-xs text-blue-600 px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100">
                    {{ __('app.modifier_mode') }}
                </button>
                <button wire:click="supprimer({{ $mode->id }})" wire:confirm="{{ __('app.supprimer_mode') }}"
                    class="text-xs text-red-400 hover:text-red-600 px-2 py-1">
                    ✕
                </button>
            </div>
        </div>
        @empty
        <div class="py-16 text-center text-gray-400">
            <p class="text-3xl mb-2">💳</p>
            <p class="text-sm">{{ __('app.aucun_mode') }}</p>
        </div>
        @endforelse
    </div>

    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-sm rounded-t-2xl sm:rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-lg">{{ $modeId ? __('app.modifier_mode') : __('app.nouveau_mode') }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.nom') }} *</label>
                    <input wire:model="nom" type="text" autofocus
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('nom') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-3">
                    <input wire:model="actif" type="checkbox" id="actif_check" class="w-4 h-4 text-blue-600 rounded">
                    <label for="actif_check" class="text-sm text-gray-700">{{ __('app.activer_mode') }}</label>
                </div>
            </div>
            <div class="px-6 pb-6 flex gap-3">
                <button wire:click="$set('showModal', false)"
                    class="flex-1 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700">{{ __('app.annuler') }}</button>
                <button wire:click="sauvegarder"
                    class="flex-1 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700">{{ __('app.sauvegarder') }}</button>
            </div>
        </div>
    </div>
    @endif

</div>
