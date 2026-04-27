<?php

if (!function_exists('num')) {
    /**
     * Formate un nombre avec espace insécable (U+00A0) comme séparateur de milliers.
     * L'espace insécable est traité comme un caractère neutre par l'algorithme bidi Unicode,
     * ce qui évite l'inversion des chiffres en mode RTL (arabe).
     * Exemple : num(10000) → "10 000"  (avec U+00A0, pas U+0020)
     */
    function num(float|int $value, int $decimals = 0): string
    {
        return number_format($value, $decimals, ',', "\xc2\xa0"); // U+00A0 = espace insécable
    }
}
