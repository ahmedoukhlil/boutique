<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $facture->numero }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @page { size: A5; margin: 0; }
        @media print {
            body { margin: 0; background: white; }
            .no-print { display: none !important; }
            .page { box-shadow: none !important; border: none !important; page-break-after: avoid; }
        }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f3f4f6; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-start py-6 px-4 bg-gray-100">

    {{-- Boutons impression --}}
    <div class="no-print mb-5 flex gap-3">
        <button onclick="window.print()"
            class="bg-blue-600 text-white px-6 py-2 rounded-xl font-semibold hover:bg-blue-700 text-sm">
            🖨️ Imprimer
        </button>
        <a href="javascript:history.back()"
            class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded-xl font-semibold hover:bg-gray-50 text-sm">
            ← Retour
        </a>
    </div>

    {{-- Page A5 --}}
    <div class="page bg-white w-[148mm] shadow-xl rounded-xl overflow-hidden text-[11px] leading-snug">

        {{-- En-tête --}}
        <div class="bg-blue-700 text-white px-7 py-5 flex items-center gap-4">
            @php $logoPath = $p['logo'] ?? null; @endphp
            @if($logoPath)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath) }}"
                class="h-14 w-14 object-contain rounded-lg bg-white/10 p-1 shrink-0" alt="Logo">
            @endif
            <div class="flex-1 min-w-0">
                <p class="font-bold text-lg leading-tight">{{ $p['nom_boutique'] ?? config('app.name') }}</p>
                @if(!empty($p['slogan']))
                <p class="text-blue-200 text-xs mt-0.5">{{ $p['slogan'] }}</p>
                @endif
                <div class="flex flex-wrap gap-x-4 gap-y-0.5 mt-1.5 text-blue-100 text-[10px]">
                    @if(!empty($p['telephone'])) <span>📞 {{ $p['telephone'] }}@if(!empty($p['telephone2'])) · {{ $p['telephone2'] }}@endif</span> @endif
                    @if(!empty($p['email'])) <span>✉️ {{ $p['email'] }}</span> @endif
                    @if(!empty($p['adresse'])) <span>📍 {{ $p['adresse'] }}@if(!empty($p['ville'])), {{ $p['ville'] }}@endif</span> @endif
                </div>
            </div>
            <div class="text-right shrink-0">
                <p class="text-blue-200 text-[10px] uppercase tracking-wide">Facture</p>
                <p class="font-bold text-base">{{ $facture->numero }}</p>
                <p class="text-blue-200 text-[10px] mt-0.5">{{ $facture->created_at->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="px-7 py-5 space-y-5">

            {{-- Client --}}
            @if($facture->client)
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Facturé à</p>
                    <p class="font-bold text-gray-800 text-sm">{{ $facture->client->nom_complet }}</p>
                    @if($facture->client->telephone) <p class="text-gray-500">{{ $facture->client->telephone }}</p> @endif
                    @if($facture->client->adresse) <p class="text-gray-500">{{ $facture->client->adresse }}</p> @endif
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Statut</p>
                    @if($facture->statut === 'payee')
                        <span class="inline-block bg-green-100 text-green-700 font-semibold px-3 py-0.5 rounded-full text-[10px]">Payée</span>
                    @elseif($facture->statut === 'partielle')
                        <span class="inline-block bg-amber-100 text-amber-700 font-semibold px-3 py-0.5 rounded-full text-[10px]">Partielle</span>
                    @else
                        <span class="inline-block bg-gray-100 text-gray-600 font-semibold px-3 py-0.5 rounded-full text-[10px]">{{ ucfirst($facture->statut) }}</span>
                    @endif
                </div>
            </div>
            @else
            <div class="flex justify-end">
                <div class="text-right">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Statut</p>
                    <span class="inline-block bg-green-100 text-green-700 font-semibold px-3 py-0.5 rounded-full text-[10px]">Payée</span>
                </div>
            </div>
            @endif

            {{-- Tableau des articles --}}
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left py-2 px-3 font-semibold text-gray-600 text-[10px] uppercase tracking-wide">Désignation</th>
                        <th class="text-center py-2 px-2 font-semibold text-gray-600 text-[10px] uppercase tracking-wide w-12">Qté</th>
                        <th class="text-right py-2 px-3 font-semibold text-gray-600 text-[10px] uppercase tracking-wide w-24">P.U.</th>
                        <th class="text-right py-2 px-3 font-semibold text-gray-600 text-[10px] uppercase tracking-wide w-24">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($facture->lignes as $ligne)
                    <tr>
                        <td class="py-2 px-3">
                            <p class="font-medium text-gray-800">{{ $ligne->designation }}</p>
                            @php
                                $infos = array_filter([
                                    $ligne->variante?->produit?->categorie?->nom,
                                    $ligne->variante?->produit?->marque?->nom,
                                ]);
                            @endphp
                            @if($infos)
                            <p class="text-gray-400 text-[9px]">{{ implode(' · ', $infos) }}</p>
                            @endif
                        </td>
                        <td class="py-2 px-2 text-center text-gray-700">{{ $ligne->quantite }}</td>
                        <td class="py-2 px-3 text-right text-gray-700">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} {{ $p['devise'] ?? 'MRU' }}</td>
                        <td class="py-2 px-3 text-right font-semibold text-gray-800">{{ number_format($ligne->total_ligne, 0, ',', ' ') }} {{ $p['devise'] ?? 'MRU' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Totaux --}}
            <div class="flex justify-end">
                <div class="w-56 space-y-1.5">
                    @if($facture->remise_montant > 0)
                    <div class="flex justify-between text-gray-500">
                        <span>Sous-total</span>
                        <span>{{ number_format($facture->sous_total, 0, ',', ' ') }} {{ $p['devise'] ?? 'MRU' }}</span>
                    </div>
                    <div class="flex justify-between text-red-500">
                        <span>Remise {{ $facture->remise_pourcent > 0 ? '('.$facture->remise_pourcent.'%)' : '' }}</span>
                        <span>-{{ number_format($facture->remise_montant, 0, ',', ' ') }} {{ $p['devise'] ?? 'MRU' }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-sm border-t-2 border-gray-800 pt-2 mt-1 text-gray-900">
                        <span>TOTAL TTC</span>
                        <span class="text-blue-700">{{ number_format($facture->total_ttc, 0, ',', ' ') }} {{ $p['devise'] ?? 'MRU' }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500 border-t border-gray-100 pt-1">
                        <span>Reçu ({{ ucfirst($facture->mode_paiement) }})</span>
                        <span>{{ number_format($facture->montant_recu, 0, ',', ' ') }} {{ $p['devise'] ?? 'MRU' }}</span>
                    </div>
                    @if($facture->monnaie_rendue > 0)
                    <div class="flex justify-between text-green-600 font-medium">
                        <span>Monnaie rendue</span>
                        <span>{{ number_format($facture->monnaie_rendue, 0, ',', ' ') }} {{ $p['devise'] ?? 'MRU' }}</span>
                    </div>
                    @endif
                    @if($facture->statut === 'partielle')
                    @php $reste = $facture->total_ttc - $facture->montant_recu; @endphp
                    <div class="flex justify-between text-red-600 font-bold border-t border-dashed border-red-200 pt-1">
                        <span>⚠️ Reste à régler</span>
                        <span>{{ number_format($reste, 0, ',', ' ') }} {{ $p['devise'] ?? 'MRU' }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Pied de page --}}
            @if(!empty($p['pied_page']))
            <div class="border-t border-dashed border-gray-200 pt-4 text-center text-gray-400 text-[10px]">
                {{ $p['pied_page'] }}
            </div>
            @endif

        </div>
    </div>

</body>
</html>
