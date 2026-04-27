<div class="max-w-2xl space-y-6 pb-20 lg:pb-0">

    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ __('app.parametres_titre') }}</h1>
        <p class="text-sm text-gray-500">{{ __('app.infos_boutique') }}</p>
    </div>

    @if($saved)
    <div x-data x-init="setTimeout(() => $wire.set('saved', false), 3000)"
        class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        {{ __('app.sauvegarde_ok') }}
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">

        {{-- Logo --}}
        <div class="px-6 py-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">{{ __('app.logo') }}</h2>
            <div class="flex items-center gap-5">
                @php $logoPath = \App\Models\Parametre::get('logo'); @endphp
                @if($logoPath)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath) }}"
                    class="h-16 w-16 object-contain rounded-xl border border-gray-200 bg-gray-50" alt="Logo">
                @else
                <div class="h-16 w-16 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50">
                    <span class="text-2xl">🏪</span>
                </div>
                @endif
                <div class="flex-1">
                    <input wire:model="logo" type="file" accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG — max 2 Mo</p>
                </div>
            </div>
        </div>

        {{-- Identité --}}
        <div class="px-6 py-5 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">{{ __('app.identite') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.nom_boutique') }} *</label>
                    <input wire:model="nom_boutique" type="text"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('nom_boutique') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.slogan') }}</label>
                    <input wire:model="slogan" type="text"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Coordonnées --}}
        <div class="px-6 py-5 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">{{ __('app.coordonnees') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.telephone_principal') }}</label>
                    <input wire:model="telephone" type="tel"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.telephone_secondaire') }}</label>
                    <input wire:model="telephone2" type="tel"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.email') }}</label>
                    <input wire:model="email" type="email"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.ville') }}</label>
                    <input wire:model="ville" type="text"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.adresse_complete') }}</label>
                    <input wire:model="adresse" type="text"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>

        {{-- Facture --}}
        <div class="px-6 py-5 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700">{{ __('app.facture_section') }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.devise') }} *</label>
                    <input wire:model="devise" type="text" placeholder="MRU"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('devise') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.pied_page') }}</label>
                    <textarea wire:model="pied_page" rows="2"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>
            </div>
        </div>

    </div>

    <div class="flex justify-end">
        <button wire:click="sauvegarder"
            class="bg-blue-600 text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-blue-700 text-sm">
            {{ __('app.sauvegarder_btn') }}
        </button>
    </div>

    {{-- Langue de l'interface --}}
    <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
        <div class="px-6 py-5">
            <h2 class="text-sm font-semibold text-gray-700 mb-1">{{ __('app.langue') }}</h2>
            <p class="text-xs text-gray-400 mb-4">{{ __('app.langue_desc') }}</p>
            <div class="flex gap-3">
                <a href="{{ route('langue.changer', 'fr') }}"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-semibold transition-colors
                       {{ app()->getLocale() === 'fr'
                           ? 'bg-blue-600 text-white border-blue-600'
                           : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    <span class="text-base">🇫🇷</span> Français
                </a>
                <a href="{{ route('langue.changer', 'ar') }}"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-semibold transition-colors
                       {{ app()->getLocale() === 'ar'
                           ? 'bg-blue-600 text-white border-blue-600'
                           : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                    <span class="text-base">🇲🇷</span> العربية
                </a>
            </div>
        </div>
    </div>

</div>
