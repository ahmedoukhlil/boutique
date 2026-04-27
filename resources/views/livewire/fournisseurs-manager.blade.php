<div class="space-y-5 pb-20 lg:pb-0">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('app.fournisseurs') }}</h1>
            <p class="text-sm text-gray-500">{{ __('app.gestion_fournisseurs') }}</p>
        </div>
        <button wire:click="nouveau"
            class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2.5 rounded-xl hover:bg-blue-700 text-sm">
            {{ __('app.nouveau_fournisseur') }}
        </button>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($this->fournisseurs as $f)
        <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center text-xl">🏭</div>
                <button wire:click="editer({{ $f->id }})"
                    class="text-xs text-blue-600 px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100">
                    {{ __('app.editer_btn') }}
                </button>
            </div>
            <p class="font-semibold text-gray-900">{{ $f->nom }}</p>
            @if($f->contact) <p class="text-xs text-gray-500 mt-1">👤 {{ $f->contact }}</p> @endif
            @if($f->telephone) <p class="text-xs text-gray-500">📞 {{ $f->telephone }}</p> @endif
            @if($f->email) <p class="text-xs text-gray-500">✉️ {{ $f->email }}</p> @endif
            <p class="text-xs text-gray-400 mt-3 border-t border-gray-100 pt-2">{{ $f->lots_count }} {{ __('app.lots_receptionnes') }}</p>
        </div>
        @empty
        <div class="col-span-full py-16 text-center">
            <span class="text-4xl">🏭</span>
            <p class="mt-3 text-gray-400">{{ __('app.aucun_fournisseur') }}</p>
        </div>
        @endforelse
    </div>

    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-lg">{{ $fournisseurId ? __('app.modifier') : __('app.nouveau_fournisseur') }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.nom') }} *</label>
                    <input wire:model="nom" type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('nom') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.contact') }}</label>
                        <input wire:model="contact" type="text" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
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
                    <div>
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

</div>
