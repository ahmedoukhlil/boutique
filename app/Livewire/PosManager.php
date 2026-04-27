<?php

namespace App\Livewire;

use App\Models\CaisseOperation;
use App\Models\Client;
use App\Models\Facture;
use App\Models\LigneFacture;
use App\Models\ModePaiement;
use App\Models\ReglementClient;
use App\Models\VarianteProduit;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;

class PosManager extends Component
{
    public array $panier = [];
    public float $totalPanier = 0;
    public float $remisePourcent = 0;
    public float $montantRecu = 0;
    public float $monnaieRendue = 0;
    public string $modePaiement = 'especes';
    public string $searchArticle = '';
    public ?int $clientId = null;
    public ?string $clientSearch = '';
    public bool $showClientSearch = false;
    public ?int $factureCreeeId = null;
    public bool $showTicket = false;

    // Création rapide client
    public bool $showModalNouveauClient = false;
    public string $newClientTelephone = '';
    public string $newClientNom = '';
    public string $newClientPrenom = '';

    #[Computed]
    public function resultatsRecherche()
    {
        $query = VarianteProduit::with('produit.categorie', 'produit.marque')
            ->where('variantes_produit.actif', true)
            ->where('variantes_produit.quantite_stock', '>', 0)
            ->whereHas('produit', fn($q) => $q->where('actif', true));

        if (strlen($this->searchArticle) >= 2) {
            $search = $this->searchArticle;
            $query->where(fn($q) => $q
                ->where('variantes_produit.code_barre', $search)
                ->orWhereHas('produit', fn($q2) => $q2
                    ->where('nom', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('code_barre', $search)
                )
            );
        }

        $limit = strlen($this->searchArticle) >= 2 ? 12 : 24;

        // Subquery for sales totals to avoid MySQL ONLY_FULL_GROUP_BY error
        $ventesSub = DB::table('lignes_facture')
            ->selectRaw('variante_id, COALESCE(SUM(quantite), 0) as total_vendu')
            ->groupBy('variante_id');

        $rows = $query
            ->leftJoinSub($ventesSub, 'ventes', 'ventes.variante_id', '=', 'variantes_produit.id')
            ->selectRaw('variantes_produit.id, COALESCE(ventes.total_vendu, 0) as total_vendu')
            ->orderByDesc('total_vendu')
            ->orderByDesc('variantes_produit.quantite_stock')
            ->limit($limit)
            ->pluck('total_vendu', 'id');

        $variantes = VarianteProduit::with('produit.categorie', 'produit.marque')
            ->whereIn('id', $rows->keys())
            ->get()
            ->sortByDesc(fn($v) => $rows[$v->id])
            ->each(fn($v) => $v->total_vendu = $rows[$v->id]);

        return $variantes->values();
    }

    #[Computed]
    public function modesPaiement()
    {
        return ModePaiement::actif()->get();
    }

    #[Computed]
    public function clients()
    {
        if (strlen($this->clientSearch) < 2) return collect();
        return Client::where('actif', true)
            ->where(fn($q) => $q
                ->where('nom', 'like', "%{$this->clientSearch}%")
                ->orWhere('telephone', 'like', "%{$this->clientSearch}%")
            )
            ->limit(5)->get();
    }

    public function updatedClientSearch(): void
    {
        // Si la recherche ressemble à un numéro de 8 chiffres et ne trouve rien → proposer création
        if (strlen($this->clientSearch) >= 8 && $this->clients->isEmpty()) {
            $this->newClientTelephone = $this->clientSearch;
            $this->newClientNom = '';
            $this->newClientPrenom = '';
            $this->showModalNouveauClient = true;
        }
    }

    public function creerClientRapide(): void
    {
        $this->validate([
            'newClientTelephone' => 'required|string|max:20',
            'newClientNom'       => 'nullable|string|max:80',
            'newClientPrenom'    => 'nullable|string|max:80',
        ]);

        $client = Client::create([
            'telephone'  => $this->newClientTelephone,
            'nom'        => $this->newClientNom ?: 'Client',
            'prenom'     => $this->newClientPrenom ?: null,
            'actif'      => true,
        ]);

        $this->clientId = $client->id;
        $this->clientSearch = '';
        $this->showModalNouveauClient = false;
        $this->reset(['newClientTelephone', 'newClientNom', 'newClientPrenom']);
        unset($this->clients, $this->clientSelectionne);
    }

    public function annulerNouveauClient(): void
    {
        $this->showModalNouveauClient = false;
        $this->clientSearch = '';
        $this->reset(['newClientTelephone', 'newClientNom', 'newClientPrenom']);
    }

    #[Computed]
    public function clientSelectionne()
    {
        return $this->clientId ? Client::find($this->clientId) : null;
    }

    public function ajouterAuPanier(int $varianteId, int $quantite = 1): void
    {
        $variante = VarianteProduit::with('produit.categorie', 'produit.marque')->find($varianteId);
        if (!$variante || $variante->quantite_stock < 1) {
            $this->addError('stock', "Stock insuffisant pour {$variante?->produit->nom}");
            return;
        }

        $key = "v_{$varianteId}";
        if (isset($this->panier[$key])) {
            $nouvelleQte = $this->panier[$key]['quantite'] + $quantite;
            if ($nouvelleQte > $variante->quantite_stock) {
                $this->addError('stock', "Stock max atteint : {$variante->quantite_stock}");
                return;
            }
            $this->panier[$key]['quantite'] = $nouvelleQte;
            $this->panier[$key]['total'] = round($nouvelleQte * $this->panier[$key]['prix'], 2);
        } else {
            $this->panier[$key] = [
                'variante_id' => $varianteId,
                'nom' => $variante->produit->nom,
                'libelle_variante' => $variante->libelle,
                'prix' => $variante->prix_final,
                'quantite' => $quantite,
                'total' => round($quantite * $variante->prix_final, 2),
                'stock_max' => $variante->quantite_stock,
                'image' => $variante->produit->image,
                'categorie' => $variante->produit->categorie?->nom ?? '',
                'marque' => $variante->produit->marque?->nom ?? '',
            ];
        }

        $this->searchArticle = '';
        $this->recalculer();
    }

    public function modifierQuantite(string $key, int $delta): void
    {
        if (!isset($this->panier[$key])) return;
        $nouvelleQte = $this->panier[$key]['quantite'] + $delta;
        if ($nouvelleQte <= 0) {
            $this->retirerDuPanier($key);
            return;
        }
        if ($nouvelleQte > $this->panier[$key]['stock_max']) return;
        $this->panier[$key]['quantite'] = $nouvelleQte;
        $this->panier[$key]['total'] = round($nouvelleQte * $this->panier[$key]['prix'], 2);
        $this->recalculer();
    }

    public function retirerDuPanier(string $key): void
    {
        unset($this->panier[$key]);
        $this->recalculer();
    }

    public function viderPanier(): void
    {
        $this->panier = [];
        $this->recalculer();
        $this->montantRecu = 0;
        $this->monnaieRendue = 0;
        $this->clientId = null;
        $this->remisePourcent = 0;
    }

    public function recalculer(): void
    {
        $sous = collect($this->panier)->sum('total');
        $remise = $this->remisePourcent > 0 ? round($sous * $this->remisePourcent / 100, 2) : 0;
        $this->totalPanier = max(0, $sous - $remise);
        $this->montantRecu = $this->totalPanier;
        $this->monnaieRendue = 0;
    }

    public function calculerMonnaie(): void
    {
        $this->monnaieRendue = max(0, round($this->montantRecu - $this->totalPanier, 2));
    }

    public function validerVente(): void
    {
        if (empty($this->panier)) return;

        $montantRecu = $this->montantRecu ?: 0;
        $estPartiel = $montantRecu > 0 && $montantRecu < $this->totalPanier;

        // Paiement partiel uniquement si client sélectionné
        if ($estPartiel && !$this->clientId) {
            $this->addError('paiement', 'Sélectionnez un client pour un paiement partiel');
            return;
        }

        // Paiement complet : vérifier montant suffisant (sauf si client avec crédit)
        if (!$estPartiel && $montantRecu > 0 && $montantRecu < $this->totalPanier) {
            $this->addError('paiement', 'Montant reçu insuffisant');
            return;
        }

        $stockService = app(StockService::class);

        DB::transaction(function () use ($stockService, $montantRecu, $estPartiel) {
            $resteARegler = $estPartiel ? round($this->totalPanier - $montantRecu, 2) : 0;

            $facture = Facture::create([
                'numero'         => Facture::genererNumero(),
                'client_id'      => $this->clientId,
                'user_id'        => auth()->id(),
                'sous_total'     => collect($this->panier)->sum('total'),
                'remise_pourcent'=> $this->remisePourcent,
                'remise_montant' => collect($this->panier)->sum('total') - $this->totalPanier,
                'total_ttc'      => $this->totalPanier,
                'montant_recu'   => $montantRecu ?: $this->totalPanier,
                'monnaie_rendue' => $this->monnaieRendue,
                'mode_paiement'  => $this->modePaiement,
                'statut'         => $estPartiel ? 'partielle' : 'payee',
            ]);

            foreach ($this->panier as $item) {
                $ligne = LigneFacture::create([
                    'facture_id'    => $facture->id,
                    'variante_id'   => $item['variante_id'],
                    'designation'   => $item['nom'] . ($item['libelle_variante'] !== 'Standard' ? ' - ' . $item['libelle_variante'] : ''),
                    'quantite'      => $item['quantite'],
                    'prix_unitaire' => $item['prix'],
                    'remise_pourcent' => 0,
                    'total_ligne'   => $item['total'],
                    'type'          => 'produit',
                ]);
                $variante = VarianteProduit::find($item['variante_id']);
                $stockService->deduireStockFifo($variante, $item['quantite'], $ligne->id);
            }

            CaisseOperation::create([
                'facture_id'     => $facture->id,
                'user_id'        => auth()->id(),
                'type'           => 'VENTE',
                'montant'        => $montantRecu ?: $this->totalPanier,
                'mode_paiement'  => $this->modePaiement,
                'date_operation' => now()->toDateString(),
            ]);

            // Mise à jour solde client si paiement partiel
            if ($estPartiel && $this->clientId) {
                $client = Client::find($this->clientId);
                $client->decrement('solde', $resteARegler);

                ReglementClient::create([
                    'client_id'     => $this->clientId,
                    'facture_id'    => $facture->id,
                    'montant'       => -$resteARegler,
                    'type'          => 'dette',
                    'mode_paiement' => $this->modePaiement,
                    'note'          => "Reste à régler sur facture {$facture->numero}",
                ]);
            }

            $this->factureCreeeId = $facture->id;
        });

        $this->showTicket = true;
        $this->viderPanier();
    }

    public function nouvelleVente(): void
    {
        $this->showTicket = false;
        $this->factureCreeeId = null;
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.pos-manager')->layout('layouts.boutique');
    }
}
