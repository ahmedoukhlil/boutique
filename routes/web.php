<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Livewire\Dashboard;
use App\Livewire\PosManager;
use App\Livewire\CatalogueManager;
use App\Livewire\StockManager;
use App\Livewire\CaisseManager;
use App\Livewire\ClientsManager;
use App\Livewire\FacturesManager;
use App\Livewire\CategoriesManager;
use App\Livewire\FournisseursManager;
use App\Livewire\ModesPaiementManager;
use App\Livewire\MarquesManager;
use App\Livewire\ParametresManager;
use App\Livewire\UtilisateursManager;
use App\Livewire\PermissionsManager;

Route::get('/', fn() => redirect()->route('dashboard'));
Route::get('/langue/{locale}', [\App\Http\Controllers\LangueController::class, 'changer'])->name('langue.changer');

Route::middleware(['auth', 'role'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/pos',      PosManager::class)->middleware('role:pos.acceder')->name('pos');
    Route::get('/catalogue',CatalogueManager::class)->middleware('role:catalogue.voir')->name('catalogue');
    Route::get('/stock',    StockManager::class)->middleware('role:stock.voir')->name('stock');
    Route::get('/caisse',   CaisseManager::class)->middleware('role:caisse.voir')->name('caisse');
    Route::get('/clients',  ClientsManager::class)->middleware('role:clients.voir')->name('clients');
    Route::get('/factures', FacturesManager::class)->middleware('role:factures.voir')->name('factures');
    Route::get('/categories',  CategoriesManager::class)->middleware('role:categories.gerer')->name('categories');
    Route::get('/fournisseurs',FournisseursManager::class)->middleware('role:fournisseurs.gerer')->name('fournisseurs');
    Route::get('/marques',     MarquesManager::class)->middleware('role:marques.gerer')->name('marques');
    Route::get('/modes-paiement', ModesPaiementManager::class)->middleware('role:paiements.gerer')->name('modes-paiement');
    Route::get('/parametres',    ParametresManager::class)->middleware('role:parametres.gerer')->name('parametres');
    Route::get('/utilisateurs',  UtilisateursManager::class)->middleware('role:utilisateurs.gerer')->name('utilisateurs');
    Route::get('/permissions',   PermissionsManager::class)->middleware('role:utilisateurs.gerer')->name('permissions');

    Route::get('/factures/{id}/ticket', function ($id) {
        $facture = \App\Models\Facture::with('lignes.variante.produit', 'client', 'user')->findOrFail($id);
        return view('factures.ticket', compact('facture'));
    })->name('factures.ticket');

    Route::get('/factures/{id}', function ($id) {
        $facture = \App\Models\Facture::with('lignes.variante.produit.categorie', 'lignes.variante.produit.marque', 'client', 'user')->findOrFail($id);
        $p = \App\Models\Parametre::tous();
        return view('factures.facture-a5', compact('facture', 'p'));
    })->name('factures.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
