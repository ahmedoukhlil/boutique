<div class="space-y-6 pb-20 lg:pb-0">

    <div>
        <h1 class="text-xl font-bold text-gray-900">{{ __('app.permissions_titre') }}</h1>
        <p class="text-sm text-gray-500">{{ __('app.permissions_desc') }}</p>
    </div>

    @if($saved)
    <div x-data x-init="setTimeout(() => $wire.set('saved', false), 3000)"
        class="bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        {{ __('app.permissions_sauvees') }}
    </div>
    @endif

    {{-- Sélecteur de rôle --}}
    <div class="flex gap-2 flex-wrap">
        @foreach($roles as $code => $libelle)
        @php
            $colors = match($code) {
                'admin'        => ['active' => 'bg-purple-600 text-white', 'inactive' => 'bg-white border border-purple-200 text-purple-700 hover:bg-purple-50'],
                'gestionnaire' => ['active' => 'bg-blue-600 text-white',   'inactive' => 'bg-white border border-blue-200 text-blue-700 hover:bg-blue-50'],
                default        => ['active' => 'bg-green-600 text-white',  'inactive' => 'bg-white border border-green-200 text-green-700 hover:bg-green-50'],
            };
        @endphp
        <button wire:click="setRole('{{ $code }}')"
            class="px-5 py-2.5 rounded-xl font-semibold text-sm transition-colors {{ $roleActif === $code ? $colors['active'] : $colors['inactive'] }}">
            {{ $libelle }}
        </button>
        @endforeach
    </div>

    @if($roleActif === 'admin')
    <div class="bg-purple-50 border border-purple-200 text-purple-700 text-sm px-4 py-3 rounded-xl">
        {{ __('app.admin_locked') }}
    </div>
    @endif

    {{-- Tableau des permissions --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        @foreach($permissionsParModule as $module => $perms)
        <div class="border-b border-gray-100 last:border-0">
            <div class="px-5 py-3 bg-gray-50 flex items-center justify-between">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $module }}</p>
                @if($roleActif !== 'admin')
                <div class="flex gap-3 text-xs">
                    <button wire:click="$set('permissionsActives', array_values(array_unique(array_merge($permissionsActives, {{ json_encode($perms->pluck('cle')->toArray()) }}))))"
                        class="text-blue-600 hover:text-blue-800 font-medium">{{ __('app.tout_cocher') }}</button>
                    <button wire:click="$set('permissionsActives', array_values(array_diff($permissionsActives, {{ json_encode($perms->pluck('cle')->toArray()) }})))"
                        class="text-red-500 hover:text-red-700 font-medium">{{ __('app.tout_decocher') }}</button>
                </div>
                @endif
            </div>
            <div class="divide-y divide-gray-50">
                @foreach($perms as $perm)
                <label class="flex items-center gap-4 px-5 py-3 cursor-pointer hover:bg-gray-50 transition-colors
                    {{ $roleActif === 'admin' ? 'opacity-60 cursor-default' : '' }}">
                    <input type="checkbox"
                        wire:model="permissionsActives"
                        value="{{ $perm->cle }}"
                        @if($roleActif === 'admin') checked disabled @endif
                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">{{ $perm->label }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $perm->cle }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    @if($roleActif !== 'admin')
    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">
            <span class="font-semibold text-gray-700">{{ count($permissionsActives) }}</span>
            {{ __('app.nb_permissions') }}
            <span class="font-semibold text-gray-700">{{ $roles[$roleActif] }}</span>
        </p>
        <button wire:click="sauvegarder"
            class="bg-blue-600 text-white font-semibold px-6 py-2.5 rounded-xl hover:bg-blue-700 text-sm">
            {{ __('app.sauvegarder_btn') }}
        </button>
    </div>
    @endif

</div>
