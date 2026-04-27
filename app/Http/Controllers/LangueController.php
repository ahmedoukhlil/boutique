<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LangueController extends Controller
{
    public function changer(string $locale)
    {
        if (!in_array($locale, ['fr', 'ar'])) abort(400);
        session(['locale' => $locale]);
        return back();
    }
}
