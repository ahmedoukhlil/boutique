<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $facture->numero }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
            .ticket { box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="no-print mb-4 text-center">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-semibold hover:bg-blue-700 mr-2">
            🖨️ Imprimer
        </button>
        <a href="javascript:history.back()" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-xl font-semibold hover:bg-gray-300">
            ← Retour
        </a>
    </div>

    <div class="ticket bg-white w-80 rounded-2xl shadow-lg overflow-hidden font-mono text-sm">
        {{-- En-tête --}}
        <div class="bg-blue-600 text-white text-center py-5 px-4">
            <p class="font-bold text-lg">Pièces détachées</p>
            <p class="text-blue-200 text-xs mt-0.5">Pièces détachées</p>
        </div>

        <div class="px-5 py-4 space-y-3">
            {{-- Infos facture --}}
            <div class="text-center border-b border-dashed border-gray-300 pb-3">
                <p class="font-bold text-gray-800">{{ $facture->numero }}</p>
                <p class="text-xs text-gray-500">{{ $facture->created_at->format('d/m/Y à H:i') }}</p>
                @if($facture->client)
                <p class="text-xs text-gray-600 mt-1">Client : {{ $facture->client->nom_complet }}</p>
                @endif
            </div>

            {{-- Lignes --}}
            <div class="space-y-2">
                @foreach($facture->lignes as $ligne)
                <div class="flex justify-between items-start gap-2">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs leading-tight font-medium">{{ $ligne->designation }}</p>
                        @php
                            $infos = array_filter([
                                $ligne->variante?->produit?->categorie?->nom,
                                $ligne->variante?->produit?->marque?->nom,
                            ]);
                        @endphp
                        @if($infos)
                        <p class="text-xs text-gray-400 leading-tight">{{ implode(' · ', $infos) }}</p>
                        @endif
                        <p class="text-xs text-gray-400">{{ $ligne->quantite }} × {{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} MRU</p>
                    </div>
                    <p class="text-xs font-bold whitespace-nowrap">{{ number_format($ligne->total_ligne, 0, ',', ' ') }}</p>
                </div>
                @endforeach
            </div>

            {{-- Totaux --}}
            <div class="border-t border-dashed border-gray-300 pt-3 space-y-1">
                @if($facture->remise_montant > 0)
                <div class="flex justify-between text-xs text-gray-500">
                    <span>Sous-total</span>
                    <span>{{ number_format($facture->sous_total, 0, ',', ' ') }} MRU</span>
                </div>
                <div class="flex justify-between text-xs text-red-500">
                    <span>Remise {{ $facture->remise_pourcent > 0 ? '('.$facture->remise_pourcent.'%)' : '' }}</span>
                    <span>-{{ number_format($facture->remise_montant, 0, ',', ' ') }} MRU</span>
                </div>
                @endif
                <div class="flex justify-between font-bold text-base border-t border-gray-200 pt-2 mt-1">
                    <span>TOTAL</span>
                    <span class="text-blue-600">{{ number_format($facture->total_ttc, 0, ',', ' ') }} MRU</span>
                </div>
                <div class="flex justify-between text-xs text-gray-500">
                    <span>Reçu ({{ ucfirst($facture->mode_paiement) }})</span>
                    <span>{{ number_format($facture->montant_recu, 0, ',', ' ') }} MRU</span>
                </div>
                @if($facture->monnaie_rendue > 0)
                <div class="flex justify-between text-xs text-green-600 font-semibold">
                    <span>Monnaie rendue</span>
                    <span>{{ number_format($facture->monnaie_rendue, 0, ',', ' ') }} MRU</span>
                </div>
                @endif
                @if($facture->statut === 'partielle')
                @php $reste = $facture->total_ttc - $facture->montant_recu; @endphp
                <div class="flex justify-between text-xs text-red-600 font-bold border-t border-dashed border-red-200 pt-1 mt-1">
                    <span>⚠️ Reste à régler</span>
                    <span>{{ number_format($reste, 0, ',', ' ') }} MRU</span>
                </div>
                @endif
            </div>

            {{-- Pied --}}
            <div class="border-t border-dashed border-gray-300 pt-3 text-center text-xs text-gray-400 space-y-1">
                <p class="font-semibold text-gray-600">Merci de votre visite !</p>
                <p>Échange sous 7 jours avec ticket</p>
            </div>
        </div>
    </div>

</body>
</html>
