<div class="space-y-5 pb-20 lg:pb-0">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ __('app.utilisateurs_titre') }}</h1>
            <p class="text-sm text-gray-500">{{ __('app.gestion_acces') }}</p>
        </div>
        <button wire:click="nouvelUtilisateur"
            class="inline-flex items-center gap-2 bg-blue-600 text-white font-semibold px-4 py-2.5 rounded-xl hover:bg-blue-700 text-sm">
            {{ __('app.nouvel_utilisateur') }}
        </button>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="relative search-wrapper">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 search-icon">🔍</span>
            <input wire:model.live.debounce.300ms="search" type="text"
                placeholder="{{ __('app.rechercher_user') }}"
                class="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3 font-semibold text-gray-600">{{ __('app.utilisateurs') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 hidden sm:table-cell">{{ __('app.email') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-center">{{ __('app.role') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-center">{{ __('app.statut') }}</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-right">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($utilisateurs as $u)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0
                                    {{ $u->role === 'admin' ? 'bg-purple-100' : ($u->role === 'gestionnaire' ? 'bg-blue-100' : 'bg-green-100') }}">
                                    <span class="text-sm font-bold
                                        {{ $u->role === 'admin' ? 'text-purple-700' : ($u->role === 'gestionnaire' ? 'text-blue-700' : 'text-green-700') }}">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $u->name }}</p>
                                    @if($u->id === auth()->id())
                                    <p class="text-xs text-blue-500">{{ __('app.vous') }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 hidden sm:table-cell">{{ $u->email }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $badgeClass = match($u->role) {
                                    'admin'        => 'bg-purple-100 text-purple-700',
                                    'gestionnaire' => 'bg-blue-100 text-blue-700',
                                    default        => 'bg-green-100 text-green-700',
                                };
                            @endphp
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                {{ $u->role_libelle }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleActif({{ $u->id }})"
                                @if($u->id === auth()->id()) disabled @endif
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold transition-colors
                                    {{ $u->actif ? 'bg-green-100 text-green-700 hover:bg-red-100 hover:text-red-600' : 'bg-red-100 text-red-600 hover:bg-green-100 hover:text-green-700' }}
                                    disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="w-1.5 h-1.5 rounded-full {{ $u->actif ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $u->actif ? __('app.actif_label') : __('app.inactif_label') }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button wire:click="editer({{ $u->id }})"
                                class="text-xs text-blue-600 hover:text-blue-800 px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100">
                                {{ __('app.editer_btn') }}
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">{{ __('app.aucun_utilisateur') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">{{ $utilisateurs->links() }}</div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white w-full sm:max-w-md rounded-t-2xl sm:rounded-2xl">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-lg">{{ $userId ? __('app.modifier_utilisateur') : __('app.nouvel_utilisateur') }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.nom_complet') }} *</label>
                    <input wire:model="name" type="text"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.email') }} *</label>
                    <input wire:model="email" type="email"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.role') }} *</label>
                    <select wire:model="role"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        @foreach($roles as $code => $libelle)
                        <option value="{{ $code }}">{{ $libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3">
                    <input wire:model="actif" type="checkbox" id="actif_check"
                        class="w-4 h-4 text-blue-600 rounded border-gray-300">
                    <label for="actif_check" class="text-sm font-medium text-gray-700">{{ __('app.compte_actif') }}</label>
                </div>
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-xs text-gray-500 mb-3">{{ $userId ? __('app.laisser_vide_mdp') : __('app.mot_de_passe') . ' *' }}</p>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.mot_de_passe') }}</label>
                            <input wire:model="password" type="password"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 mb-1 block">{{ __('app.confirmation_mdp') }}</label>
                            <input wire:model="password_confirmation" type="password"
                                class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
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
