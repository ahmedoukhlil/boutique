# Guide : Système de Facturation, Vente & Gestion de Stock
## Cabinet Savwa — Adaptation à d'autres types d'activités

---

## 1. Vue d'ensemble du système actuel

Cabinet Savwa est un ERP médical développé en **Laravel 10 + Livewire 2**. Il intègre nativement :

- Facturation patients avec gestion des assurances
- Encaissement multi-mode (espèces, carte, chèque…)
- Gestion de stock pharmaceutique avec traçabilité par lot (FIFO)
- Journal de caisse et reporting financier

Le cœur du système repose sur **4 entités principales** réutilisables dans n'importe quelle activité commerciale :

```
Facture  →  DetailFacture  →  Produit/Article
    ↓
CaisseOperation  →  MouvementStock (FIFO)
```

---

## 2. Architecture du système de facturation

### 2.1 Cycle de vie d'une facture

```
1. Création de la facture (numéro unique auto-généré)
       ↓
2. Ajout d'articles/services (validation stock en temps réel)
       ↓
3. Calcul automatique des totaux (TTC, part assurance si applicable)
       ↓
4. Enregistrement du paiement → CaisseOperation
       ↓
5. Déduction du stock (FIFO) + traçabilité MouvementStock
```

### 2.2 Fichiers clés du système

| Fichier | Rôle |
|---|---|
| [app/Models/Facture.php](app/Models/Facture.php) | Modèle facture, génération numéro unique, statut paiement |
| [app/Models/Detailfacturepatient.php](app/Models/Detailfacturepatient.php) | Lignes de facture (articles, services) |
| [app/Models/StockMedicament.php](app/Models/StockMedicament.php) | Stock général par article |
| [app/Models/LotMedicament.php](app/Models/LotMedicament.php) | Lots avec dates d'expiration, FIFO |
| [app/Models/MouvementStock.php](app/Models/MouvementStock.php) | Journal complet des mouvements stock |
| [app/Models/CaisseOperation.php](app/Models/CaisseOperation.php) | Opérations de caisse |
| [app/Http/Livewire/ReglementFacture.php](app/Http/Livewire/ReglementFacture.php) | Interface principale facturation + paiement |
| [app/Http/Livewire/PharmacieManager.php](app/Http/Livewire/PharmacieManager.php) | Dashboard stock |

---

## 3. Comment le système déduit le stock (logique FIFO)

Quand un article stocké est ajouté à une facture :

```
1. Récupérer les lots actifs triés par date d'expiration (plus ancienne en premier)
2. Déduire la quantité lot par lot jusqu'à atteindre la quantité demandée
3. Créer un MouvementStock (type=SORTIE) par lot consommé
4. Mettre à jour StockMedicament.quantiteStock global
5. En cas de suppression de ligne → restauration automatique du stock
```

Le champ `IsAct` dans `Detailfacturepatient` distingue le type de ligne :
- `1` = Acte/Service (pas de stock)
- `2` = Produit physique (déclenche la déduction stock)
- `3` = Analyse (pas de stock)
- `4` = Radio/Imagerie (pas de stock)

---

## 4. Adaptation à d'autres types d'activités

### 4.1 Boutique de vêtements / Mode

**Ce qui change :**

| Concept médical | Équivalent mode |
|---|---|
| `Medicament` | Article (référence produit) |
| `LotMedicament` | Lot d'achat avec variantes (taille, couleur) |
| `Acte` | Service (retouche, livraison…) |
| `Patient` | Client |
| `Assurance` | Programme fidélité / bon de réduction |
| `fkidtype` | Catégorie (Homme, Femme, Enfant, Accessoire) |

**Adaptations techniques à faire :**

```php
// Dans Medicament → renommer ou créer Article
// Ajouter des champs : taille, couleur, reference_fournisseur, code_barre
// Dans LotMedicament → garder la logique FIFO
// Ajouter : numeroCommande, saison (Ete2025, Hiver2025)

// Dans Detailfacturepatient
// IsAct = 2 pour tout article physique (déclenche déduction stock)
// IsAct = 1 pour services (retouches, emballage cadeau)

// Dans StockMedicament → renommer StockArticle
// Ajouter : emplacement (rayon, étagère), seuil_alerte
```

**Alertes stock à activer :**
- Rupture de stock par taille/couleur
- Articles en fin de saison (équivalent expiration)
- Seuil minimum par référence

---

### 4.2 Pharmacie indépendante

C'est l'activité la **plus proche** du système existant. Presque aucune modification majeure n'est nécessaire.

**Ce qui est déjà en place :**
- Gestion des lots avec dates de péremption
- FIFO automatique (vend le plus ancien en premier)
- Alertes expirations (30 jours avant)
- Traçabilité lot → patient → facture
- Dashboard stock (`PharmacieManager`)

**Adaptations mineures :**

```php
// Ajouter : DCI (Dénomination Commune Internationale), dosage, forme galénique
// Ajouter : numéro AMM (Autorisation de Mise sur le Marché)
// Patient → Client (sans dossier médical)
// Assurance → Mutuelle/Sécurité Sociale (logique TXPEC déjà présente)
// Ajouter : ordonnance liée à la facture (champ fkidOrdonnance)
```

**Ce qui fonctionne sans modification :**
- Vente directe via `PharmacieManager` (onglet Ventes)
- Réapprovisionnement par lot avec fournisseur
- Journal des mouvements stock
- Caisse journalière

---

### 4.3 Restaurant

**Différences majeures :**
- Stock = ingrédients, pas de produits finis à stocker
- La "vente" est une commande de table
- Pas de péremption par lot mais gestion DLC des ingrédients
- Un plat = nomenclature (liste d'ingrédients)

**Mapping conceptuel :**

| Concept médical | Équivalent restaurant |
|---|---|
| `Medicament` | Ingrédient (farine, huile, viande…) |
| `Acte` | Plat du menu |
| `Detailfacturepatient` | Ligne de commande |
| `Patient` | Table / Client |
| `LotMedicament` | Lot de livraison fournisseur avec DLC |

**Adaptations spécifiques :**

```php
// Nouvelle table : nomenclatures_plats
// plat_id, ingredient_id, quantite_utilisee
// Lors de la vente d'un plat → déduire automatiquement les ingrédients

// Exemple : "Tagine d'agneau"
// → Agneau : -300g
// → Pommes de terre : -200g
// → Épices : -10g

// IsAct = 1 pour les plats (pas de stock direct)
// Créer un trigger/listener qui décompose le plat en ingrédients
// et appelle deduireStockMedicament() pour chaque ingrédient

// Ajouter : numero_table, nombre_couverts, statut_commande (en attente, servi, payé)
```

**Points à conserver :**
- Alertes stock faible (ingrédients sous le seuil)
- FIFO pour les ingrédients périssables (DLC)
- Journal caisse journalier

---

### 4.4 Épicerie / Supérette / Commerce général

**Le système est quasi-directement applicable.**

**Adaptations :**

```php
// Ajouter : code_barre EAN-13 dans Medicament (Article)
// Ajouter : TVA par catégorie
// Ajouter : prix_promo, date_debut_promo, date_fin_promo
// Patient → Client (avec programme fidélité optionnel)

// Dans LotMedicament → ajouter date_reception, bon_livraison
// Utiliser dateExpiration pour la DLC des produits alimentaires

// Tickets de caisse simplifés (pas d'assurance)
// TXPEC = 0 pour tous les clients (non assurés)
```

**Ce qui fonctionne sans modification :**
- Facturation article par article
- Paiement multi-mode (espèces, carte)
- Déduction stock immédiate à la vente
- Réapprovisionnement par lot avec fournisseur

---

### 4.5 Point de Vente (POS — caisse enregistreuse)

Un POS est une interface de vente rapide orientée caissier : scan d'article, calcul immédiat, encaissement en quelques secondes. C'est la couche UI à ajouter par-dessus le système existant.

**Différences par rapport à la facturation classique :**

| Facturation classique | POS |
|---|---|
| Sélection client obligatoire | Client anonyme possible |
| Ajout article ligne par ligne | Scan code-barres ou recherche rapide |
| Paiement différé possible | Paiement immédiat à la caisse |
| Facture éditée et archivée | Ticket de caisse simple |
| Interface bureau | Interface tactile plein écran |

**Architecture d'un POS avec le système existant :**

```
[Interface POS Livewire]
    ↓ (scan code-barres / recherche)
Medicament / Article  →  StockMedicament (validation dispo)
    ↓
Panier en session (wire:model)
    ↓
Facture (client anonyme ou sélectionné)  →  Detailfacturepatient
    ↓
CaisseOperation (paiement immédiat)  →  MouvementStock (FIFO)
    ↓
Impression ticket thermique
```

**Nouveau composant Livewire à créer : `PosManager`**

```php
class PosManager extends Component
{
    // Panier
    public array $panier = [];          // [article_id => [nom, prix, qte, stock_id]]
    public float $totalPanier = 0;
    public float $montantRecu = 0;      // Argent remis par le client
    public float $monnaieRendue = 0;

    // Recherche article
    public string $searchArticle = '';
    public int $modeReglement = 1;      // 1=Espèces, 2=Carte, 3=Chèque

    // Client optionnel
    public ?int $clientId = null;

    public function ajouterAuPanier($articleId, $quantite = 1): void
    {
        // 1. Vérifier stock disponible
        // 2. Ajouter ou incrémenter dans $this->panier
        // 3. Recalculer $this->totalPanier
    }

    public function retirerDuPanier($articleId): void { ... }

    public function calculerMonnaie(): void
    {
        $this->monnaieRendue = max(0, $this->montantRecu - $this->totalPanier);
    }

    public function validerVente(): void
    {
        DB::transaction(function () {
            // 1. Créer Facture (client anonyme si non sélectionné)
            // 2. Créer Detailfacturepatient pour chaque ligne panier
            // 3. Déduire stock FIFO (appeler deduireStockMedicament())
            // 4. Créer CaisseOperation
            // 5. Vider le panier → prêt pour la prochaine vente
        });
    }

    public function imprimerTicket($factureId): void
    {
        // Rediriger vers ReglementFactureController::showReceipt()
        // ou générer un ticket thermique simplifié (80mm)
    }
}
```

**Adaptations pour l'interface POS :**

```php
// Vue Blade plein écran (resources/views/livewire/pos-manager.blade.php)
// Layout en 2 colonnes :
//   Gauche : recherche article + grille d'articles favoris (raccourcis)
//   Droite : panier en cours + total + paiement + monnaie rendue

// Recherche article : wire:keydown.enter pour valider rapidement
// Support scan code-barres USB (simule frappe clavier → déclenche wire:keydown.enter)

// Bouton paiement espèces : calcul monnaie en temps réel
// Bouton paiement carte : pas de monnaie à rendre
// Bouton paiement mixte : espèces + carte (cas fréquent)
```

**Ticket thermique (format 80mm) :**

```blade
{{-- resources/views/pos/ticket.blade.php --}}
<div style="width:302px; font-family:monospace; font-size:12px;">
    <p style="text-align:center">{{ $commerce->nom }}</p>
    <p style="text-align:center">{{ $commerce->adresse }}</p>
    <hr>
    @foreach($facture->details as $ligne)
    <div>{{ $ligne->Actes }} x{{ $ligne->Quantite }} ... {{ $ligne->PrixFacture }} MRU</div>
    @endforeach
    <hr>
    <div><strong>TOTAL : {{ $facture->TotFacture }} MRU</strong></div>
    <div>Reçu : {{ $montantRecu }} MRU</div>
    <div>Monnaie : {{ $monnaieRendue }} MRU</div>
    <hr>
    <p style="text-align:center">Merci de votre visite !</p>
</div>
```

**Ce qui est réutilisé sans modification depuis le système existant :**
- Logique FIFO (`deduireStockMedicament()`)
- Création `Facture` + `Detailfacturepatient`
- `CaisseOperation` pour la caisse journalière
- Validation stock en temps réel
- Journal des mouvements (`MouvementStock`)

**Fonctionnalités POS à développer :**
- [ ] Interface tactile plein écran (mode kiosk)
- [ ] Grille articles favoris / raccourcis
- [ ] Support scan code-barres USB (natif via input focus)
- [ ] Paiement mixte (espèces + carte)
- [ ] Impression ticket thermique 80mm
- [ ] Ouverture/fermeture de caisse (fond de caisse initial)
- [ ] Mode hors-ligne avec synchronisation différée
- [ ] Remises rapides (%) à la ligne ou sur le total
- [ ] Annulation dernière vente (remboursement immédiat)

---

## 5. Tableau comparatif des adaptations par secteur

| Fonctionnalité | Cabinet Médical | Pharmacie | Boutique Mode | Restaurant | Épicerie |
|---|:---:|:---:|:---:|:---:|:---:|
| Facturation client | ✅ | ✅ | ✅ | ✅ | ✅ |
| Caisse journalière | ✅ | ✅ | ✅ | ✅ | ✅ |
| Gestion stock articles | ✅ | ✅ | ✅ | ⚠️* | ✅ |
| Lots + FIFO | ✅ | ✅ | ✅ | ✅ | ✅ |
| Alertes péremption | ✅ | ✅ | ⚠️** | ✅ | ✅ |
| Gestion assurance | ✅ | ✅ | ❌ | ❌ | ❌ |
| Nomenclature produit | ❌ | ❌ | ❌ | ✅*** | ❌ |
| Code-barres | ❌ | ⚠️ | ✅ | ❌ | ✅ |
| Variantes (taille/couleur) | ❌ | ❌ | ✅ | ❌ | ❌ |
| Tables/Commandes | ❌ | ❌ | ❌ | ✅ | ❌ |

*Stock ingrédients seulement  
**Fin de saison plutôt qu'expiration  
***À développer (décomposition plat → ingrédients)

---

## 6. Plan d'adaptation technique recommandé

### Étape 1 — Renommer les entités métier (sans toucher à la logique)

```bash
# Dans les vues Blade et composants Livewire, remplacer les labels :
# "Médicament"  →  "Produit" / "Article" / "Ingrédient"
# "Patient"     →  "Client"
# "Cabinet"     →  "Point de vente"
# "Médecin"     →  "Vendeur" / "Caissier"
```

### Étape 2 — Ajouter les champs spécifiques à l'activité

```php
// Migration exemple pour boutique :
Schema::table('medicaments', function (Blueprint $table) {
    $table->string('taille')->nullable();
    $table->string('couleur')->nullable();
    $table->string('code_barre')->nullable()->unique();
    $table->string('saison')->nullable(); // "Ete2025"
    $table->string('marque')->nullable();
});
```

### Étape 3 — Désactiver les modules inutiles

```php
// Dans le menu/navigation, masquer selon l'activité :
// - Module Assurance (boutique, restaurant, épicerie)
// - Module Consultations (tout sauf médical)
// - Module Ordonnances (tout sauf médical/pharmacie)
// Utiliser les gates et policies Laravel existantes
```

### Étape 4 — Adapter les alertes et tableaux de bord

```php
// PharmacieManager → renommer en StockManager
// Adapter getStatistiques() pour l'activité cible :
// Boutique : articles par saison, rotation par catégorie
// Restaurant : ingrédients sous seuil, coût matière par plat
// Épicerie : ruptures, valeur stock, marge brute
```

### Étape 5 — Adapter les impressions

```php
// ReglementFactureController::showReceipt()
// Modifier le template de ticket/facture :
// - En-tête : nom du commerce, logo, adresse, NIF/RCCM
// - Pied : CGV, mention TVA, programme fidélité
// - Retirer : mentions médicales, N° dossier patient
```

---

## 7. Fonctionnalités à développer selon l'activité

### Pour une boutique de vêtements
- [ ] Gestion des variantes (taille S/M/L/XL × couleur)
- [ ] Lecteur code-barres (intégration JS ou scanner USB)
- [ ] Gestion des retours/échanges (mouvementStock type=RETOUR)
- [ ] Soldes et promotions avec date de fin

### Pour une pharmacie indépendante
- [ ] Liaison ordonnance ↔ facture
- [ ] Module fournisseurs avec bon de commande
- [ ] Remise automatique carte de fidélité
- [ ] Export état des ventes par DCI

### Pour un restaurant
- [ ] Table des nomenclatures (recettes)
- [ ] Interface commande par table avec statut
- [ ] Décomposition automatique plat → ingrédients
- [ ] Rapport coût matière / marge par plat

### Pour une épicerie
- [ ] Import catalogue fournisseur (CSV/Excel)
- [ ] Mode caisse rapide (recherche par code-barres)
- [ ] Gestion TVA multi-taux
- [ ] Inventaire périodique avec ajustement automatique

---

## 8. Ce qui est réutilisable à 100% sans modification

Les composants suivants fonctionnent **tels quels** pour toute activité :

1. **Génération de numéro de facture unique** — `Facture::generateUniqueFactureNumber()` avec verrou transactionnel
2. **Caisse journalière** — `CaisseOperationsManager` avec filtre par vendeur/date
3. **FIFO automatique** — `deduireStockMedicament()` dans `ReglementFacture`
4. **Alertes stock faible** — Scopes `stockFaible()` et `actifs()` dans `StockMedicament`
5. **Journal des mouvements** — `MouvementStock` avec types ENTREE/SORTIE/AJUSTEMENT
6. **Reçu de paiement imprimable** — `ReglementFactureController::showReceipt()`
7. **Multi-mode de paiement** — Table `ref_type_paiement` configurable
8. **Multi-point de vente** — Isolation par `fkidcabinet` déjà en place

---

## 9. Recommandation finale

> Le système de Cabinet Savwa est architecturalement **générique**. La couche métier médicale (consultations, ordonnances, patients) est séparable de la couche commerce (facturation + stock + caisse).
>
> Pour une nouvelle activité, la stratégie la plus rapide est :
> 1. Conserver le backend tel quel
> 2. Créer de nouvelles vues Livewire adaptées au secteur
> 3. Ajouter les champs métier via migrations
> 4. Désactiver les modules non pertinents via les policies Laravel

La base de données et la logique FIFO/caisse/facturation représentent **80% du travail** — ils sont déjà prêts.

---

*Document généré le 2026-04-26 — basé sur l'analyse du code source de Cabinet Savwa*
