<x-guest-layout>

    <h2 class="text-lg font-bold text-gray-900 mb-6">Connexion</h2>

    {{-- Message d'erreur de session --}}
    @if (session('status'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                Adresse e-mail
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email') }}"
                   required autofocus autocomplete="username"
                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition
                          @error('email') border-red-400 bg-red-50 @enderror">
            @error('email')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Mot de passe --}}
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                Mot de passe
            </label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password"
                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm
                          focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition
                          @error('password') border-red-400 bg-red-50 @enderror">
            @error('password')
            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Se souvenir de moi --}}
        <div class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember"
                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
            <label for="remember_me" class="text-sm text-gray-600 cursor-pointer select-none">
                Se souvenir de moi
            </label>
        </div>

        {{-- Bouton --}}
        <button type="submit"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-xl
                   text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            Se connecter
        </button>

    </form>

</x-guest-layout>
