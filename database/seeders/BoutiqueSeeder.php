<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Client;
use App\Models\Fournisseur;
use App\Models\LotProduit;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\VarianteProduit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BoutiqueSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@boutique.com'],
            ['name' => 'Admin', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        // Catégories
        $categories = [
            ['nom' => 'Vêtements Femme', 'icone' => '👗', 'couleur' => '#e879f9'],
            ['nom' => 'Vêtements Homme', 'icone' => '👔', 'couleur' => '#60a5fa'],
            ['nom' => 'Chaussures', 'icone' => '👟', 'couleur' => '#34d399'],
            ['nom' => 'Sacs & Maroquinerie', 'icone' => '👜', 'couleur' => '#fb923c'],
            ['nom' => 'Parfums & Beauté', 'icone' => '🧴', 'couleur' => '#a78bfa'],
            ['nom' => 'Accessoires', 'icone' => '💍', 'couleur' => '#fbbf24'],
            ['nom' => 'Enfant', 'icone' => '🧒', 'couleur' => '#f472b6'],
        ];

        $cats = [];
        foreach ($categories as $cat) {
            $cats[$cat['nom']] = Categorie::firstOrCreate(
                ['slug' => Str::slug($cat['nom'])],
                array_merge($cat, ['actif' => true])
            );
        }

        // Marques
        $marques = ['Zara', 'H&M', 'Nike', 'Adidas', 'Chanel', 'Gucci', 'Local Brand'];
        $mqObjs = [];
        foreach ($marques as $nom) {
            $mqObjs[$nom] = Marque::firstOrCreate(
                ['slug' => Str::slug($nom)],
                ['nom' => $nom, 'actif' => true]
            );
        }

        // Fournisseurs
        $fournisseur = Fournisseur::firstOrCreate(
            ['nom' => 'Fournisseur Principal'],
            ['contact' => 'Ali Moussa', 'telephone' => '22201020', 'actif' => true]
        );

        // Produits
        $produits = [
            ['nom' => 'Robe Florale Été', 'cat' => 'Vêtements Femme', 'marque' => 'Zara', 'prix' => 1500, 'genre' => 'Femme', 'saison' => 'Ete2025',
             'variantes' => [['taille' => 'S'], ['taille' => 'M'], ['taille' => 'L'], ['taille' => 'XL']]],
            ['nom' => 'Jean Slim Homme', 'cat' => 'Vêtements Homme', 'marque' => 'H&M', 'prix' => 1200, 'genre' => 'Homme',
             'variantes' => [['taille' => '38'], ['taille' => '40'], ['taille' => '42'], ['taille' => '44']]],
            ['nom' => 'Sneakers Running', 'cat' => 'Chaussures', 'marque' => 'Nike', 'prix' => 2800, 'genre' => 'Unisexe',
             'variantes' => [['taille' => '38'], ['taille' => '39'], ['taille' => '40'], ['taille' => '41'], ['taille' => '42'], ['taille' => '43']]],
            ['nom' => 'Sac à Main Cuir', 'cat' => 'Sacs & Maroquinerie', 'marque' => 'Local Brand', 'prix' => 3500, 'genre' => 'Femme',
             'variantes' => [['couleur' => 'Noir', 'code_couleur' => '#000000'], ['couleur' => 'Marron', 'code_couleur' => '#8B4513'], ['couleur' => 'Beige', 'code_couleur' => '#F5F5DC']]],
            ['nom' => 'Eau de Parfum Chanel N°5', 'cat' => 'Parfums & Beauté', 'marque' => 'Chanel', 'prix' => 8500, 'genre' => 'Femme',
             'variantes' => [['taille' => '50ml'], ['taille' => '100ml']]],
            ['nom' => 'T-Shirt Basique', 'cat' => 'Vêtements Homme', 'marque' => 'H&M', 'prix' => 450, 'genre' => 'Homme',
             'variantes' => [
                ['taille' => 'S', 'couleur' => 'Blanc', 'code_couleur' => '#FFFFFF'],
                ['taille' => 'M', 'couleur' => 'Blanc', 'code_couleur' => '#FFFFFF'],
                ['taille' => 'L', 'couleur' => 'Blanc', 'code_couleur' => '#FFFFFF'],
                ['taille' => 'S', 'couleur' => 'Noir', 'code_couleur' => '#000000'],
                ['taille' => 'M', 'couleur' => 'Noir', 'code_couleur' => '#000000'],
             ]],
            ['nom' => 'Sandales Été Femme', 'cat' => 'Chaussures', 'marque' => 'Zara', 'prix' => 950, 'genre' => 'Femme', 'saison' => 'Ete2025',
             'variantes' => [['taille' => '36'], ['taille' => '37'], ['taille' => '38'], ['taille' => '39'], ['taille' => '40']]],
            ['nom' => 'Collier Doré', 'cat' => 'Accessoires', 'marque' => 'Local Brand', 'prix' => 650,
             'variantes' => [['taille' => 'Unique']]],
        ];

        foreach ($produits as $data) {
            $variantes = $data['variantes'];
            unset($data['variantes']);
            $catNom = $data['cat'];
            $marqueNom = $data['marque'];
            unset($data['cat'], $data['marque']);

            $prixVente = $data['prix'];
            unset($data['prix']);
            $ref = 'ART' . str_pad(rand(1000, 9999), 5, '0', STR_PAD_LEFT);
            $produit = Produit::firstOrCreate(
                ['reference' => $ref],
                array_merge($data, [
                    'categorie_id' => $cats[$catNom]->id,
                    'marque_id' => $mqObjs[$marqueNom]->id,
                    'prix_vente' => $prixVente,
                    'prix_achat' => $prixVente * 0.6,
                    'stock_alerte' => 3,
                    'has_variantes' => true,
                    'actif' => true,
                ])
            );

            foreach ($variantes as $v) {
                $variante = VarianteProduit::firstOrCreate(
                    array_merge(['produit_id' => $produit->id], $v),
                    ['quantite_stock' => 0, 'actif' => true]
                );

                // Ajouter un lot si pas de stock
                if ($variante->quantite_stock === 0) {
                    $qte = rand(5, 20);
                    $variante->update(['quantite_stock' => $qte]);
                    LotProduit::create([
                        'variante_id' => $variante->id,
                        'fournisseur_id' => $fournisseur->id,
                        'quantite_initiale' => $qte,
                        'quantite_restante' => $qte,
                        'prix_achat_unitaire' => $produit->prix_achat,
                        'date_reception' => now()->subDays(rand(1, 60)),
                        'actif' => true,
                    ]);
                }
            }
        }

        // Clients
        $clientsData = [
            ['nom' => 'Mariem', 'prenom' => 'Ba', 'telephone' => '22301020', 'email' => 'mariem@example.com', 'points_fidelite' => 150],
            ['nom' => 'Ahmed', 'prenom' => 'Ould Saleck', 'telephone' => '22401020', 'points_fidelite' => 80],
            ['nom' => 'Fatima', 'prenom' => 'Mint Ali', 'telephone' => '22501020', 'points_fidelite' => 320],
            ['nom' => 'Mohamed', 'prenom' => 'Diallo', 'telephone' => '22601020', 'points_fidelite' => 0],
        ];

        foreach ($clientsData as $client) {
            Client::firstOrCreate(['telephone' => $client['telephone'] ?? null], $client + ['actif' => true]);
        }
    }
}
