<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion — {{ \App\Models\Parametre::get('nom_boutique', 'Pièces détachées') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm">

        {{-- Logo / En-tête --}}
        <div class="text-center mb-8">
            @php $logo = \App\Models\Parametre::get('logo'); @endphp
            @if($logo)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logo) }}"
                 alt="Logo" class="h-16 w-16 object-contain mx-auto mb-4 rounded-2xl border border-gray-100 shadow-sm bg-white p-1">
            @else
            <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/>
                </svg>
            </div>
            @endif
            <h1 class="text-xl font-bold text-gray-900">{{ \App\Models\Parametre::get('nom_boutique', 'Pièces détachées') }}</h1>
            @if(\App\Models\Parametre::get('slogan'))
            <p class="text-sm text-gray-400 mt-0.5">{{ \App\Models\Parametre::get('slogan') }}</p>
            @endif
        </div>

        {{-- Carte --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-8 py-8">
            {{ $slot }}
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; {{ date('Y') }} {{ \App\Models\Parametre::get('nom_boutique', 'Pièces détachées') }}
        </p>
    </div>

</body>
</html>
