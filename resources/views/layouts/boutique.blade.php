<!DOCTYPE html>
@php $isAr = app()->getLocale() === 'ar'; @endphp
<html lang="{{ app()->getLocale() }}" dir="{{ $isAr ? 'rtl' : 'ltr' }}" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? __('app.tableau_de_bord') }} — {{ \App\Models\Parametre::get('nom_boutique', 'Pièces détachées') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    @if($isAr)
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    @else
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @if($isAr)
    <style>
        /* ── Police arabe ── */
        body { font-family: 'Tajawal', sans-serif; }

        /* ── Tableaux ──
           En RTL, la 1ère colonne doit être à droite.
           On remet text-left en text-right et text-right en text-left. */
        [dir="rtl"] table { direction: rtl; }
        [dir="rtl"] th { text-align: right; }
        [dir="rtl"] td { text-align: right; }
        [dir="rtl"] th.text-center,
        [dir="rtl"] td.text-center  { text-align: center !important; }
        [dir="rtl"] th.text-right,
        [dir="rtl"] td.text-right   { text-align: left !important; }

        /* ── Numéros de facture (font-mono) : forcer LTR ──
           num() utilise U+00A0 pour les montants, mais les N° de facture
           sont encore en font-mono sans num(). */
        [dir="rtl"] .font-mono {
            direction: ltr !important;
            unicode-bidi: isolate !important;
            display: inline-block;
        }

        /* ── Inputs, selects, textareas ── */
        [dir="rtl"] input:not([type="checkbox"]):not([type="radio"]):not([type="color"]):not([type="file"]),
        [dir="rtl"] select,
        [dir="rtl"] textarea {
            text-align: right;
            direction: rtl;
        }
        /* Les champs numériques : direction LTR mais aligné à droite */
        [dir="rtl"] input[type="number"],
        [dir="rtl"] input[type="date"] {
            direction: ltr;
            text-align: right;
        }

        /* ── Labels de formulaire ── */
        [dir="rtl"] label.block { text-align: right; }
        /* Checkboxes : label flex → inverser l'ordre icône/texte */
        [dir="rtl"] label.inline-flex,
        [dir="rtl"] label.flex { flex-direction: row-reverse; }

        /* ── Boutons inline-flex avec gap (ex: ➕ Nouveau) ── */
        [dir="rtl"] button.inline-flex.gap-2,
        [dir="rtl"] a.inline-flex.gap-2,
        [dir="rtl"] button.inline-flex.gap-3,
        [dir="rtl"] a.inline-flex.gap-3 { flex-direction: row-reverse; }

        /* ── Pagination ── */
        [dir="rtl"] nav[aria-label] { direction: rtl; }

        /* ── En-têtes de page (flex justify-between) : déjà OK car symétrique ── */

        /* ── Cards stats : texte à droite ── */
        [dir="rtl"] .space-y-1 p,
        [dir="rtl"] .space-y-0\.5 p { text-align: right; }

        /* ── Icône de recherche dans les inputs ──
           L'icône est absolute left-3 → on la passe à droite,
           et le padding de l'input passe de pl-9 à pr-9 */
        [dir="rtl"] .search-wrapper .search-icon {
            left: auto !important;
            right: 0.75rem !important;
        }
        [dir="rtl"] .search-wrapper input {
            padding-left: 1rem !important;
            padding-right: 2.25rem !important;
        }

        /* ── ms- me- (margin-start/end Tailwind v3) ── */
        [dir="rtl"] .ms-2 { margin-right: 0.5rem; margin-left: 0; }
        [dir="rtl"] .me-2 { margin-left: 0.5rem; margin-right: 0; }
    </style>
    @endif
</head>
<body class="h-full font-sans antialiased">

@php
$navItems = [
    [
        'route' => 'dashboard', 'label' => __('app.tableau_de_bord'),
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>',
    ],
    [
        'route' => 'pos', 'label' => __('app.caisse_pos'),
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="2" y="4" width="20" height="14" rx="2"/><path d="M8 20h8M12 18v2"/><path d="M7 9h.01M12 9h.01M17 9h.01M7 13h.01M12 13h.01M17 13h.01"/></svg>',
    ],
    [
        'route' => 'catalogue', 'label' => __('app.catalogue'),
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>',
    ],
    [
        'route' => 'stock', 'label' => __('app.stock'),
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>',
    ],
    [
        'route' => 'clients', 'label' => __('app.clients'),
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
    ],
    [
        'route' => 'factures', 'label' => __('app.factures'),
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>',
    ],
    [
        'route' => 'caisse', 'label' => __('app.caisse'),
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 6v2m0 8v2M8.5 9.5A3.5 3.5 0 0112 8c1.93 0 3.5 1.12 3.5 2.5S13.93 13 12 13s-3.5 1.12-3.5 2.5S10.07 17 12 17a3.5 3.5 0 003.5-1.5"/></svg>',
    ],
];
$adminItems = [
    [
        'route' => 'categories', 'label' => __('app.categories'), 'role' => 'gestionnaire',
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>',
    ],
    [
        'route' => 'fournisseurs', 'label' => __('app.fournisseurs'), 'role' => 'gestionnaire',
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
    ],
    [
        'route' => 'marques', 'label' => __('app.marques'), 'role' => 'gestionnaire',
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>',
    ],
    [
        'route' => 'modes-paiement', 'label' => __('app.modes_paiement'), 'role' => 'admin',
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><path d="M1 10h22"/></svg>',
    ],
    [
        'route' => 'parametres', 'label' => __('app.parametres'), 'role' => 'admin',
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>',
    ],
    [
        'route' => 'utilisateurs', 'label' => __('app.utilisateurs'), 'role' => 'admin',
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
    ],
    [
        'route' => 'permissions', 'label' => __('app.permissions'), 'role' => 'admin',
        'svg' => '<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>',
    ],
];
$mobileNav = array_slice($navItems, 0, 5);
@endphp

<div class="min-h-full flex flex-col">

    {{-- Header mobile --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30 lg:hidden">
        <div class="flex items-center justify-between px-4 py-3">
            <a href="{{ route('dashboard') }}" class="font-semibold text-gray-900">
                Pièces détachées
            </a>
        </div>
    </header>

    <div class="flex flex-1">

        {{-- Sidebar desktop --}}
        <aside class="hidden lg:flex lg:flex-col w-64 bg-white fixed inset-y-0 z-20
            {{ $isAr ? 'right-0 border-l border-gray-200' : 'left-0 border-r border-gray-200' }}">
            <div class="px-6 py-5 border-b border-gray-100">
                <p class="font-semibold text-gray-900 text-sm">{{ \App\Models\Parametre::get('nom_boutique', 'Pièces détachées') }}</p>
                @if(\App\Models\Parametre::get('slogan'))
                <p class="text-xs text-gray-400 mt-0.5">{{ \App\Models\Parametre::get('slogan') }}</p>
                @endif
            </div>

            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
                @foreach($navItems as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ $active ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}
                   {{ $isAr ? 'flex-row-reverse' : '' }}">
                    <span class="{{ $active ? 'text-blue-600' : 'text-gray-400' }} shrink-0">{!! $item['svg'] !!}</span>
                    <span class="flex-1 {{ $isAr ? 'text-right' : 'text-left' }}">{{ $item['label'] }}</span>
                </a>
                @endforeach

                @php
                    $user = auth()->user();
                    $permMap = [
                        'categories'     => 'categories.gerer',
                        'fournisseurs'   => 'fournisseurs.gerer',
                        'marques'        => 'marques.gerer',
                        'modes-paiement' => 'paiements.gerer',
                        'parametres'     => 'parametres.gerer',
                        'utilisateurs'   => 'utilisateurs.gerer',
                        'permissions'    => 'utilisateurs.gerer',
                    ];
                    $visibleAdminItems = array_filter($adminItems, function($item) use ($user, $permMap) {
                        $perm = $permMap[$item['route']] ?? null;
                        return $perm ? $user->hasPermission($perm) : true;
                    });
                @endphp
                @if(!empty($visibleAdminItems))
                <div class="pt-4 mt-3 border-t border-gray-100">
                    <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 {{ $isAr ? 'text-right' : '' }}">{{ __('app.administration') }}</p>
                    @foreach($visibleAdminItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ $active ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}
                       {{ $isAr ? 'flex-row-reverse' : '' }}">
                        <span class="{{ $active ? 'text-blue-600' : 'text-gray-400' }} shrink-0">{!! $item['svg'] !!}</span>
                        <span class="flex-1 {{ $isAr ? 'text-right' : 'text-left' }}">{{ $item['label'] }}</span>
                    </a>
                    @endforeach
                </div>
                @endif
            </nav>

            <div class="px-3 py-4 border-t border-gray-100 space-y-2">
                {{-- Utilisateur --}}
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                        <span class="text-blue-700 text-xs font-semibold">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-700 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ auth()->user()->role_libelle ?? '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="{{ __('app.deconnexion') }}" class="text-gray-400 hover:text-red-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Contenu principal --}}
        <main class="flex-1 {{ $isAr ? 'lg:mr-64' : 'lg:ml-64' }} min-h-screen">
            <div class="px-4 py-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>

    </div>

    {{-- Bottom nav mobile --}}
    <nav class="lg:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 z-30">
        <div class="grid grid-cols-5 h-16">
            @foreach($mobileNav as $item)
            <a href="{{ route($item['route']) }}"
               class="flex flex-col items-center justify-center gap-1 {{ request()->routeIs($item['route']) ? 'text-blue-600' : 'text-gray-400' }}">
                {!! $item['svg'] !!}
                <span class="text-xs font-medium">{{ explode(' ', $item['label'])[0] }}</span>
            </a>
            @endforeach
        </div>
    </nav>

    {{-- Espace bottom nav --}}
    <div class="h-16 lg:hidden"></div>

</div>

@livewireScripts
</body>
</html>
