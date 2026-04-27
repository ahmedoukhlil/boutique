<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['cle' => 'pos.acceder',         'label' => 'Accéder à la caisse (POS)',      'module' => 'POS'],
            ['cle' => 'pos.remise',           'label' => 'Appliquer une remise',            'module' => 'POS'],
            ['cle' => 'pos.paiement_partiel', 'label' => 'Accepter un paiement partiel',   'module' => 'POS'],
            ['cle' => 'catalogue.voir',       'label' => 'Voir le catalogue',               'module' => 'Catalogue'],
            ['cle' => 'catalogue.creer',      'label' => 'Créer un produit',                'module' => 'Catalogue'],
            ['cle' => 'catalogue.modifier',   'label' => 'Modifier un produit',             'module' => 'Catalogue'],
            ['cle' => 'catalogue.supprimer',  'label' => 'Supprimer un produit',            'module' => 'Catalogue'],
            ['cle' => 'stock.voir',           'label' => 'Voir le stock',                   'module' => 'Stock'],
            ['cle' => 'stock.ajuster',        'label' => 'Ajuster le stock manuellement',   'module' => 'Stock'],
            ['cle' => 'stock.approvisionner', 'label' => 'Créer un approvisionnement',      'module' => 'Stock'],
            ['cle' => 'clients.voir',         'label' => 'Voir les clients',                'module' => 'Clients'],
            ['cle' => 'clients.creer',        'label' => 'Créer un client',                 'module' => 'Clients'],
            ['cle' => 'clients.modifier',     'label' => 'Modifier un client',              'module' => 'Clients'],
            ['cle' => 'clients.reglement',    'label' => 'Enregistrer un règlement client', 'module' => 'Clients'],
            ['cle' => 'factures.voir',        'label' => 'Voir les factures',               'module' => 'Factures'],
            ['cle' => 'factures.imprimer',    'label' => 'Imprimer une facture',            'module' => 'Factures'],
            ['cle' => 'factures.annuler',     'label' => 'Annuler une facture',             'module' => 'Factures'],
            ['cle' => 'caisse.voir',          'label' => 'Voir la caisse',                  'module' => 'Caisse'],
            ['cle' => 'caisse.ouvrir',        'label' => 'Ouvrir / fermer la caisse',       'module' => 'Caisse'],
            ['cle' => 'categories.gerer',     'label' => 'Gérer les catégories',            'module' => 'Administration'],
            ['cle' => 'marques.gerer',        'label' => 'Gérer les marques',               'module' => 'Administration'],
            ['cle' => 'fournisseurs.gerer',   'label' => 'Gérer les fournisseurs',          'module' => 'Administration'],
            ['cle' => 'paiements.gerer',      'label' => 'Gérer les modes de paiement',    'module' => 'Administration'],
            ['cle' => 'parametres.gerer',     'label' => 'Gérer les paramètres',            'module' => 'Administration'],
            ['cle' => 'utilisateurs.gerer',   'label' => 'Gérer les utilisateurs',          'module' => 'Administration'],
        ];

        foreach ($permissions as $p) {
            DB::table('permissions')->updateOrInsert(['cle' => $p['cle']], $p);
        }

        $defauts = [
            'admin' => array_column($permissions, 'cle'),
            'gestionnaire' => [
                'pos.acceder','pos.remise','pos.paiement_partiel',
                'catalogue.voir','catalogue.creer','catalogue.modifier',
                'stock.voir','stock.ajuster','stock.approvisionner',
                'clients.voir','clients.creer','clients.modifier','clients.reglement',
                'factures.voir','factures.imprimer','factures.annuler',
                'caisse.voir','caisse.ouvrir',
                'categories.gerer','marques.gerer','fournisseurs.gerer',
            ],
            'caissier' => [
                'pos.acceder',
                'clients.voir','clients.creer',
                'factures.voir','factures.imprimer',
                'caisse.voir',
            ],
        ];

        foreach ($defauts as $role => $cles) {
            foreach ($cles as $cle) {
                DB::table('role_permissions')->updateOrInsert(
                    ['role' => $role, 'permission_cle' => $cle]
                );
            }
        }
    }
}
